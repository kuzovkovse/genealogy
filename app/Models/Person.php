<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Couple;
use App\Models\MemorialCandle;
use App\Models\PersonPhoto;
use App\Models\MemorialPhoto;
use App\Models\PersonEvent;
use App\Models\PersonMilitaryService;
use App\Models\PersonDocument;
use App\Services\PersonNarrativeService;

class Person extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'birth_last_name',
        'patronymic',
        'gender',
        'birth_date',
        'birth_place',
        'death_date',
        'death_place',
        'biography',
        'notes',
        'photo',
        'couple_id',
        'public_uuid',
        'family_id',
        'is_war_participant',
        'burial_cemetery',
        'burial_city',
        'burial_place',
        'burial_description',
        'burial_lat',
        'burial_lng',
    ];

    protected $casts = [
        'is_war_participant' => 'boolean',
    ];

    /* =========================================================
     * 🕊 ЖИВАЯ ФРАЗА
     * ========================================================= */

    public function getNarrativePhraseAttribute(): ?string
    {
        return app(PersonNarrativeService::class)->build($this);
    }

    /* =========================================================
     * 📸 ФОТО ЖИЗНИ
     * ========================================================= */

    public function photos(): HasMany
    {
        return $this->hasMany(PersonPhoto::class);
    }

    public function memorialPhotos(): HasMany
    {
        return $this->hasMany(MemorialPhoto::class);
    }

    /* =========================================================
     * 🕯 СВЕЧИ ПАМЯТИ
     * ========================================================= */

    public function memorialCandles(): HasMany
    {
        return $this->hasMany(MemorialCandle::class);
    }

    public function activeCandles(): HasMany
    {
        return $this->hasMany(MemorialCandle::class)
            ->where('lit_at', '>=', now()->subHours(24));
    }

    /**
     * 🔥 FIX: избегаем лишних SQL при eager loading
     */
    public function activeCandlesCount(): int
    {
        return $this->active_candles_count
            ?? $this->activeCandles()->count();
    }

    /* =========================================================
     * 🧬 РОДСТВО
     * ========================================================= */

    public function parentCouple(): BelongsTo
    {
        return $this->belongsTo(Couple::class, 'couple_id');
    }

    public function father(): ?Person
    {
        return $this->parentCouple?->person1;
    }

    public function mother(): ?Person
    {
        return $this->parentCouple?->person2;
    }

    public function couplesAsFirst(): HasMany
    {
        return $this->hasMany(Couple::class, 'person_1_id');
    }

    public function couplesAsSecond(): HasMany
    {
        return $this->hasMany(Couple::class, 'person_2_id');
    }

    /**
     * Все браки человека (Collection)
     */
    public function getCouplesAttribute()
    {
        return $this->couplesAsFirst
            ->merge($this->couplesAsSecond)
            ->values();
    }

    /**
     * Query-версия
     */
    public function couples()
    {
        return Couple::query()
            ->where('person_1_id', $this->id)
            ->orWhere('person_2_id', $this->id);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Person::class, 'couple_id');
    }

    /* =========================================================
     * ФИО
     * ========================================================= */

    public function getFullNameAttribute(): string
    {
        return trim(collect([
            $this->last_name,
            $this->first_name,
            $this->patronymic,
        ])->filter()->implode(' '));
    }

    public function fullName(): string
    {
        return $this->full_name;
    }

    /* =========================================================
     * ⏳ СОБЫТИЯ ЖИЗНИ
     * ========================================================= */

    public function events(): HasMany
    {
        return $this->hasMany(PersonEvent::class)
            ->orderBy('event_date');
    }

    /* =========================================================
     * 📄 ДОКУМЕНТЫ
     * ========================================================= */

    public function documents(): HasMany
    {
        return $this->hasMany(PersonDocument::class);
    }

    /* =========================================================
     * 🪖 УЧАСТИЕ В ВОЙНАХ
     * ========================================================= */

    public function militaryServices(): HasMany
    {
        return $this->hasMany(PersonMilitaryService::class);
    }

    /* =========================================================
     * 🏠 FAMILY SCOPE
     * ========================================================= */

    protected static function booted()
    {
        static::addGlobalScope('family', function ($query) {

            if (app()->runningInConsole()) {
                return;
            }

            if (\App\Services\FamilyContext::has()) {
                $query->where(
                    'family_id',
                    \App\Services\FamilyContext::id()
                );
            }
        });
    }

    /* =========================================================
     * LIFE PHRASE (FIX N+1)
     * ========================================================= */

    public function getLifePhraseAttribute()
    {
        if ($this->is_war_participant) {
            return 'Участник Великой Отечественной войны';
        }

        if ($this->birth_date) {

            $birth = \Carbon\Carbon::parse($this->birth_date);

            if ($this->death_date) {
                $death = \Carbon\Carbon::parse($this->death_date);
                $years = (int) $birth->diffInYears($death);

                return $years >= 80
                    ? "Прожил долгую жизнь — {$years} лет"
                    : "Прожил {$years} лет";
            }

            $years = (int) $birth->diffInYears(now());
            return "Живёт уже {$years} лет";
        }

        // 🔥 FIX: используем children_count если загружено
        $childrenCount = $this->children_count
            ?? $this->children()->count();

        if ($childrenCount > 0) {

            if ($childrenCount == 1) return 'Отец одного ребёнка';
            if ($childrenCount <= 4) return "Родитель {$childrenCount} детей";

            return "Глава большой семьи";
        }

        return null;
    }
}
