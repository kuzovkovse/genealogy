<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonMilitaryService extends Model
{
    protected $table = 'person_military_services';

    protected $fillable = [
        'person_id',
        'war_type',
        'draft_year',
        'rank',
        'service_start',
        'service_end',
        'unit',
        'awards',
        'is_killed',
        'killed_date',
        'burial_place',
        'notes',
    ];

    protected $casts = [
        'draft_year'  => 'integer',
        'service_end' => 'integer',
        'is_killed'   => 'boolean',
        'killed_date' => 'date',
    ];

    /* ======================
     | RELATIONS
     ====================== */

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(
            PersonMilitaryDocument::class,
            'person_military_service_id'
        )->orderBy('document_date');
    }

    /* ======================
     | HELPERS
     ====================== */

    public function warLabel(): string
    {
        return match ($this->war_type) {
            'ww2'          => 'Великая Отечественная война',
            'ww1'          => 'Первая мировая война',
            'afghanistan' => 'Афганская война',
            'chechnya'    => 'Чеченская война',
            default       => 'Военная служба',
        };
    }
}
