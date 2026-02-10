<?php

namespace App\Services;

use App\Models\Family;
use App\Models\Person;
use App\Models\ReminderDelivery;
use Carbon\Carbon;

class ReminderScheduler
{
    public function __construct(
        protected KinshipTextService $kinshipText
    ) {}

    public function run(): int
    {
        $count = 0;

        $today = Carbon::today();

        Family::query()->each(function (Family $family) use ($today, &$count) {

            $people = Person::where('family_id', $family->id)->get();

            foreach ($people as $person) {
                if (!$person->birth_date) {
                    continue;
                }

                $birth = Carbon::parse($person->birth_date);

                if ($birth->day !== $today->day || $birth->month !== $today->month) {
                    continue;
                }

                $title = '🎂 Сегодня важный день';
                $body  = 'Ваша семья помнит день рождения '
                    . $this->kinshipText->possessive($person, auth()->user() ?? new \App\Models\User())
                    . '.';

                $delivery = ReminderDelivery::firstOrCreate(
                    [
                        'family_id'     => $family->id,
                        'person_id'     => $person->id,
                        'type'          => 'birthday',
                        'scheduled_for' => $today,
                        'channel'       => 'email',
                    ],
                    [
                        'title' => $title,
                        'body'  => $body,
                    ]
                );

                if ($delivery->wasRecentlyCreated) {
                    $count++;
                }
            }
        });

        return $count;
    }
}
