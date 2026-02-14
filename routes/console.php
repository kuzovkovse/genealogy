<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('telegram:daily-birthdays')
    ->dailyAt('09:00');

Schedule::command('telegram:daily-fact')
    ->dailyAt('12:00');

Schedule::command('telegram:weekly-genealogy')
    ->weeklyOn(0, '18:00');
