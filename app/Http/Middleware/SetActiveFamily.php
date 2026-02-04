<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FamilyContext;
use App\Models\Family;

class SetActiveFamily
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // 👤 гость — просто пропускаем
        if (!$user) {
            return $next($request);
        }

        // 1️⃣ если семья уже выбрана в сессии
        if (session()->has('active_family_id')) {
            $family = Family::find(session('active_family_id'));

            if ($family) {
                FamilyContext::set($family);
                return $next($request);
            }

            // если в сессии мусор — чистим
            session()->forget('active_family_id');
        }

        // 2️⃣ если у пользователя есть family_id
        if ($user->family_id) {
            $family = Family::find($user->family_id);

            if ($family) {
                session(['active_family_id' => $family->id]);
                FamilyContext::set($family);
                return $next($request);
            }
        }

        // 3️⃣ семьи нет — запрещаем
        abort(403, 'АКТИВНАЯ СЕМЬЯ НЕ ВЫБРАНА');
    }
}
