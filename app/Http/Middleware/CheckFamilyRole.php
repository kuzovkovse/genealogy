<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FamilyContext;

class CheckFamilyRole
{
    /**
     * Проверка роли пользователя в активной семье
     *
     * Использование:
     * ->middleware('family.role:owner')
     * ->middleware('family.role:owner,editor')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // ❌ Нет активной семьи — маскируем как 404
        if (!FamilyContext::has()) {
            abort(404, 'Семья не найдена');
        }

        // ❌ Роль не передана — ошибка конфигурации
        if (empty($roles)) {
            abort(500, 'Не указаны роли для middleware family.role');
        }

        $currentRole = FamilyContext::role();

        // ❌ Нет прав
        if (!in_array($currentRole, $roles, true)) {
            abort(403, 'Недостаточно прав для выполнения действия');
        }

        return $next($request);
    }
}
