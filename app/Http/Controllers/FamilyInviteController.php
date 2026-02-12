<?php

namespace App\Http\Controllers;

use App\Services\FamilyInviteService;
use Illuminate\Http\Request;
use App\Models\Family;
use App\Models\User;

class FamilyInviteController extends Controller
{
    public function accept(string $token)
    {
        $invite = \App\Models\FamilyInvite::where('token', $token)->firstOrFail();

        return view('family.invite.accept', [
            'invite' => $invite,
            'family' => $invite->family,
        ]);
    }

    public function store(Request $request, Family $family)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'role'  => ['required', 'in:editor,viewer'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Пользователь с таким email не найден',
            ]);
        }

        // Уже состоит в семье?
        if ($family->users()->where('user_id', $user->id)->exists()) {
            return back()->withErrors([
                'email' => 'Этот пользователь уже состоит в семье',
            ]);
        }

        $family->users()->attach($user->id, [
            'role' => $request->role,
        ]);

        return back()->with('success', 'Приглашение отправлено');
    }
    public function acceptPost(
        string $token,
        Request $request,
        FamilyInviteService $inviteService
    ) {
        $user = $request->user();

        $family = $inviteService->acceptInvite($token, $user);

        // 🔄 Обновляем activeFamily
        session(['active_family_id' => $family->id]);
        app()->instance('activeFamily', $family);

        return redirect()
            ->route('family.users')
            ->with('success', 'Вы присоединились к семье');
    }
}
