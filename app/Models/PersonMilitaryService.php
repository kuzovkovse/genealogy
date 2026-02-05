<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonMilitaryService extends Model
{
    protected $table = 'person_military_services';

    protected $fillable = [
        'person_id',

        // тип войны
        'war_type',

        // служба
        'draft_year',
        'rank',
        'service_start',
        'service_end',
        'unit',

        // награды и документы
        'awards',
        'documents',

        // гибель
        'is_killed',
        'killed_date',
        'burial_place',

        // доп. информация
        'notes',
    ];

    protected $casts = [
        'draft_year'   => 'integer',
        'service_start'=> 'date',
        'service_end'  => 'date',

        'is_killed'    => 'boolean',
        'killed_date'  => 'date',

        'documents'    => 'array',
    ];

    /* =========================================================
     * 🔗 СВЯЗИ
     * ========================================================= */

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /* =========================================================
     * 🧠 ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ
     * ========================================================= */

    /**
     * Человек погиб во время службы
     */
    public function isKilled(): bool
    {
        return (bool) $this->is_killed;
    }

    /**
     * Есть ли награды
     */
    public function hasAwards(): bool
    {
        return !empty(trim((string) $this->awards));
    }

    /**
     * Есть ли документы
     */
    public function hasDocuments(): bool
    {
        return is_array($this->documents) && count($this->documents) > 0;
    }

    /**
     * Человек служил в конкретной войне
     */
    public function isWar(string $warType): bool
    {
        return $this->war_type === $warType;
    }

    /**
     * Человек служил в ВОВ
     */
    public function isWW2(): bool
    {
        return $this->war_type === 'ww2';
    }

    /**
     * Человек служил в Первой мировой
     */
    public function isWW1(): bool
    {
        return $this->war_type === 'ww1';
    }

    /**
     * Читаемое название войны
     */
    public function warLabel(): string
    {
        return match ($this->war_type) {
            'ww1'         => 'Первая мировая война',
            'ww2'         => 'Великая Отечественная война',
            'afghanistan'=> 'Афганская война',
            'chechnya'   => 'Чеченская война',
            default      => 'Военная служба',
        };
    }
}
