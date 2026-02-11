<?php
namespace App\Services\Reminders\Channels;

use App\Models\ReminderDelivery;
use Illuminate\Support\Facades\Mail;

class EmailReminderSender
{
    public function send(ReminderDelivery $delivery): void
    {
        $reminder = $delivery->reminder;
        $user     = $delivery->user;

        Mail::to($user->email)->send(
            new \App\Mail\FamilyReminderMail($reminder, $user)
        );

        $delivery->markAsSent();
    }
}
