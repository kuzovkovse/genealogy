<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderDelivery extends Model
{
    protected $fillable = [
        'family_id',
        'person_id',
        'user_id',
        'channel',
        'type',
        'title',
        'body',
        'scheduled_for',
        'sent_at',
        'status',
        'error',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at'       => 'datetime',
    ];

    public function markSent(): void
    {
        $this->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error'  => $error,
        ]);
    }
}
