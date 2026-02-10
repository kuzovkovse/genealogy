<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FamilyContext;
use Illuminate\Http\Request;
class FamilyUserController extends Controller
{
    /**
     * 👥 Экран участников семьи
     */
    public function index()
    {
        // Активная семья (через уже существующий контекст)
        $family = FamilyContext::require();

        // Подгружаем пользователей + роли
        $family->load('users');

        return view('family.users', compact('family'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:editor,viewer',
        ]);

        $family = FamilyContext::require();

        // текущая роль пользователя в семье
        $pivot = $family->users()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        if (!$pivot) {
            abort(404);
        }

        // 🔒 владельца менять нельзя
        if ($pivot->role === 'owner') {
            return back()->with('error', 'Нельзя изменить роль владельца');
        }

        $family->users()->updateExistingPivot(
            $user->id,
            ['role' => $request->role]
        );

        return back()->with('success', 'Роль пользователя обновлена');
    }
}
