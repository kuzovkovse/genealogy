<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('telegram:daily-birthdays')
    ->dailyAt('09:00');

Schedule::command('telegram:daily-fact')
    ->dailyAt('12:00');
