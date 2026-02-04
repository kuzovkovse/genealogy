<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyUser extends Model
{
    protected $table = 'family_users';

    protected $fillable = [
        'family_id',
        'user_id',
        'role',
        'invited_by_user_id',
        'joined_at',
    ];
}
