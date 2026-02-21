<?php

namespace App\Services;

use App\Models\Person;
use App\Models\MemorialCandle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemorialCandleService
{
    const MAX_ACTIVE = 3;
    const HOURS_LIMIT = 24;

    public function light(Person $person): int
    {
        if (!$person->death_date) {
            abort(422, 'Свечу можно зажечь только для умершего человека');
        }

        return DB::transaction(function () use ($person) {

            $since = Carbon::now()->subHours(self::HOURS_LIMIT);
            $userId = Auth::id();

            // 🔒 1. Лочим свечи этого человека за последние 24 часа
            $activeQuery = MemorialCandle::where('person_id', $person->id)
                ->where('lit_at', '>=', $since)
                ->lockForUpdate();

            $activeCount = $activeQuery->count();

            if ($activeCount >= self::MAX_ACTIVE) {
                abort(422, 'Сейчас уже горит несколько свечей. Попробуйте позже');
            }

            // 🔒 2. Проверяем свечу конкретного пользователя
            if ($userId) {
                $alreadyLit = MemorialCandle::where('person_id', $person->id)
                    ->where('user_id', $userId)
                    ->where('lit_at', '>=', $since)
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyLit) {
                    abort(422, 'Вы уже зажигали свечу в течение последних 24 часов');
                }
            }

            // 🕯 3. Создаём свечу
            MemorialCandle::create([
                'person_id' => $person->id,
                'user_id' => $userId,
                'visitor_name' => Auth::user()?->name,
                'lit_at' => now(),
            ]);

            // 🔄 4. Возвращаем актуальное число
            return MemorialCandle::where('person_id', $person->id)
                ->where('lit_at', '>=', $since)
                ->count();
        });
    }
}
