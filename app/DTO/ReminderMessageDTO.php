<?php

namespace App\DTO;

class ReminderMessageDTO
{
    public function __construct(
        public string $title,
        public string $body,
        public array  $buttons = [],
        public string $tone = 'service', // service | family
    ) {}
}
