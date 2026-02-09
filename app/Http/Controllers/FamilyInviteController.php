<?php

namespace App\Http\Controllers;

use App\Services\FamilyInviteService;
use Illuminate\Http\Request;

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
