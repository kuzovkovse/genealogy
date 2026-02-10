<?php

namespace App\Services;

use App\DTO\ReminderMessageDTO;
use App\Models\User;

class TelegramSender
{
    public function send(User $user, ReminderMessageDTO $message): void
    {
        if (!$user->telegram_chat_id) {
            return;
        }

        // TODO: здесь будет реальный API
        logger()->info('Telegram reminder', [
            'user_id' => $user->id,
            'title'   => $message->title,
            'body'    => $message->body,
        ]);
    }
}
