<?php

namespace App\Services;

use App\DTO\ReminderMessageDTO;
use App\Models\Person;
use App\Models\User;

class ReminderMessageService
{
    public function __construct(
        protected KinshipTextService $kinshipText,
    ) {}

    public function birthday(Person $person, User $user): ReminderMessageDTO
    {
        $isAlive = empty($person->death_date);

        $key  = $isAlive ? 'birthday_alive' : 'birthday_memory';
        $tone = $isAlive ? 'service' : 'family';

        $template = $this->pickTemplate($key, $tone);

        $body = $this->replaceVars(
            $template['body'],
            $person,
            $user
        );

        return new ReminderMessageDTO(
            title: $template['title'],
            body:  $body,
            buttons: [
                [
                    'text' => 'Перейти к человеку',
                    'url'  => url('/people/' . $person->id),
                ],
            ],
            tone: $tone
        );
    }

    protected function pickTemplate(string $key, string $tone): array
    {
        $set = config("reminders.$key.$tone");

        return $set[array_rand($set)];
    }

    protected function replaceVars(
        string $text,
        Person $person,
        User $user
    ): string {
        return strtr($text, [
            '{name}'       => $person->full_name,
            '{kinship}'    => $this->kinshipText->forUser($person, $user),
            '{possessive}' => $this->kinshipText->possessive($person, $user),
        ]);
    }
}
