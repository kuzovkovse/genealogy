<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MemorialCandle extends Model
{
    protected $fillable = [
        'person_id',
        'user_id',
        'visitor_name',
        'lit_at',
    ];

    protected $casts = [
        'lit_at' => 'datetime',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Активна ли свеча (24 часа)
     */
    public function isActive(): bool
    {
        return $this->lit_at !== null
            && $this->lit_at->gte(now()->subHours(24));
    }
}
