<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonEvent extends Model
{
    protected $fillable = [
        'person_id',
        'event_date',
        'type',
        'title',
        'description',
        'icon',
        'is_system',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_system' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
