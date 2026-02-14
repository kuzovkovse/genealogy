<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalFact extends Model
{
    protected $fillable = [
        'content',
        'category',
        'priority',
        'last_sent_at',
        'is_active',
        'last_shown_at',
    ];
}
