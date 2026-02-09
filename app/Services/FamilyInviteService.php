<?php

namespace App\Services;

use App\Models\Family;
use App\Models\FamilyInvite;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FamilyInviteService
{
    /**
     * Принятие приглашения в семью
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function acceptInvite(string $token, User $user): Family
    {
        return DB::transaction(function () use ($token, $user) {

            $invite = FamilyInvite::where('token', $token)->lockForUpdate()->firstOrFail();

            // 🔒 Инвайт уже принят
            if ($invite->accepted_at) {
                abort(403, 'Приглашение уже использовано');
            }

            $family = Family::lockForUpdate()->findOrFail($invite->family_id);

            // Проверяем, есть ли пользователь уже в семье
            $existingUser = $family->users()
                ->where('user_id', $user->id)
                ->first();

            if ($existingUser) {

                $currentRole = $existingUser->pivot->role;

                // 🛡️ КРИТИЧЕСКАЯ ЗАЩИТА OWNER
                if ($currentRole === 'owner') {
                    $invite->update([
                        'accepted_at' => now(),
                    ]);

                    return $family;
                }

                // ❗ Пользователь уже в семье, роль НЕ МЕНЯЕМ
                $invite->update([
                    'accepted_at' => now(),
                ]);

                return $family;
            }

            // ➕ Пользователь ещё не в семье — добавляем
            $family->users()->attach($user->id, [
                'role' => $invite->role,
                'joined_at' => now(),
            ]);

            // ✅ Помечаем инвайт как принятый
            $invite->update([
                'accepted_at' => now(),
            ]);

            return $family;
        });
    }
}
