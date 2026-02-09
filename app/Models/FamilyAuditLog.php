<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyAuditLog extends Model
{
    protected $fillable = [
        'family_id',
        'actor_user_id',
        'target_user_id',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
