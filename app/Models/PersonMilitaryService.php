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

        // награды
        'awards',

        // гибель
        'is_killed',
        'killed_date',
        'burial_place',

        // заметки
        'notes',
    ];

    protected $casts = [
        'draft_year'   => 'integer',
        'service_start'=> 'integer',
        'service_end'  => 'integer',

        'is_killed'    => 'boolean',
        'killed_date'  => 'date',
    ];

    /* =========================================================
     * 🔗 СВЯЗИ
     * ========================================================= */

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /* =========================================================
     * 🧠 ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ (КЛЮЧЕВО!)
     * ========================================================= */

    /**
     * Читаемое название войны
     */
    public function warLabel(): string
    {
        return match ($this->war_type) {
            'ww2'          => 'Великая Отечественная война',
            'ww1'          => 'Первая мировая война',
            'afghanistan' => 'Афганская война',
            'chechnya'    => 'Чеченская война',
            'other'       => 'Военная служба',
            default       => 'Военная служба',
        };
    }

    /**
     * Погиб ли человек
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
}
