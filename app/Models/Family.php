<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Family extends Model
{
    protected $fillable = [
        'name',
        'owner_user_id',
        'public_uuid',
    ];

    protected static function booted()
    {
        static::creating(function ($family) {
            if (!$family->public_uuid) {
                $family->public_uuid = (string) Str::uuid();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'family_users')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }
}
