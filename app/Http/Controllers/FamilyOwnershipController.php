<?php

namespace App\Http\Controllers;

use App\Services\FamilyOwnershipService;
use Illuminate\Http\Request;

class FamilyOwnershipController extends Controller
{
    /**
     * Экран передачи владения
     * GET /family/ownership
     */
    public function index()
    {
        $family = app('activeFamily');

        // Все участники семьи, кроме текущего owner
        $candidates = $family->users()
            ->wherePivotIn('role', ['editor', 'viewer'])
            ->get();

        return view('family.ownership', [
            'family'     => $family,
            'candidates' => $candidates,
        ]);
    }

    /**
     * Выполнение передачи владения
     * POST /family/ownership/transfer
     */
    public function transfer(
        Request $request,
        FamilyOwnershipService $service
    ) {
        $request->validate([
            'new_owner_user_id' => ['required', 'integer'],
        ]);

        $family = app('activeFamily');

        $service->transfer(
            $family->id,
            (int) $request->new_owner_user_id
        );

        return redirect('/family/users')
            ->with('success', 'Владение семьёй передано');
    }
}
