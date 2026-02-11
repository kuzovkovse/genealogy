<?php
namespace App\Mail;

use App\Models\FamilyReminder;
use App\Models\User;
use Illuminate\Mail\Mailable;

class FamilyReminderMail extends Mailable
{
    public function __construct(
        public FamilyReminder $reminder,
        public User $user
    ) {}

    public function build()
    {
        return $this
            ->subject($this->reminder->emailSubject())
            ->view('emails.family-reminder');
    }
}
