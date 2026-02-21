<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Services\MemorialCandleService;
use Illuminate\Http\Request;

class MemorialCandleController extends Controller
{
    public function __invoke(Request $request, Person $person, MemorialCandleService $service)
    {
        try {
            // по желанию: имя посетителя можно брать из формы/профиля
            $visitorName = $request->user()?->name;

            $activeCount = $service->light($person, $visitorName);

            return response()->json([
                'active_count' => $activeCount,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Действие сейчас недоступно',
            ], 422);
        }
    }
    public function memorialCandle(Person $person, CandleService $service)
    {
        $count = $service->light($person);

        return response()->json([
            'active_count' => $count,
        ]);
    }
}
