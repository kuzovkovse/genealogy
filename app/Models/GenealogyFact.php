<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenealogyFact extends Model
{
    protected $fillable = [
        'content',
        'priority',
        'category',
        'is_active',
        'last_shown_at',
    ];
}
