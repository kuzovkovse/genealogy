<?php

namespace App\Services;

use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RecentActivityService
{
    public function build(Person $person, int $limit = 5): Collection
    {
        $items = collect();

        // 📸 Фото жизни
        foreach ($person->photos()->latest()->take($limit)->get() as $photo) {
            $items->push([
                'icon' => '🖼',
                'text' => 'Добавлено фото',
                'at'   => $photo->created_at,
            ]);
        }

        // 🕯 Свечи памяти
        foreach ($person->memorialCandles()->latest('lit_at')->take($limit)->get() as $candle) {
            $items->push([
                'icon' => '🕯',
                'text' => 'Зажжена свеча',
                'at'   => $candle->lit_at,
            ]);
        }

        // 📌 Пользовательские события
        foreach ($person->events()->latest()->take($limit)->get() as $event) {
            $items->push([
                'icon' => '📌',
                'text' => 'Добавлено событие',
                'at'   => $event->created_at,
            ]);
        }

        // 📖 Биография (если менялась)
        if ($person->biography && $person->updated_at) {
            $items->push([
                'icon' => '📖',
                'text' => 'Обновлена история жизни',
                'at'   => $person->updated_at,
            ]);
        }

        return $items
            ->sortByDesc('at')
            ->take($limit)
            ->values()
            ->map(fn ($item) => [
                'icon' => $item['icon'],
                'text' => $item['text'],
                'time' => Carbon::parse($item['at'])->diffForHumans(),
            ]);
    }
}
