<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Family;

class SetActiveFamily
{
    public function handle(Request $request, Closure $next)
    {
        // 1️⃣ Если пользователь НЕ залогинен — вообще ничего не делаем
        if (!auth()->check()) {
            return $next($request);
        }

        // 2️⃣ Не лезем в auth-роуты
        if (
            $request->is('login') ||
            $request->is('register') ||
            $request->is('logout') ||
            $request->is('password/*')
        ) {
            return $next($request);
        }

        // 3️⃣ Если активная семья уже в контейнере — ок
        if (app()->has('activeFamily')) {
            return $next($request);
        }

        // 4️⃣ Пытаемся восстановить из сессии
        if (session()->has('active_family_id')) {
            $family = Family::find(session('active_family_id'));

            if ($family) {
                app()->instance('activeFamily', $family);
                return $next($request);
            }

            session()->forget('active_family_id');
        }

        // 5️⃣ Берём первую семью пользователя
        $family = auth()->user()
            ->families()
            ->orderBy('family_users.created_at')
            ->first();

        if ($family) {
            session(['active_family_id' => $family->id]);
            app()->instance('activeFamily', $family);
            return $next($request);
        }

        // 6️⃣ Если вообще нет семей
        abort(403, 'АКТИВНАЯ СЕМЬЯ НЕ ВЫБРАНА');
    }
}
