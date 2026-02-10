<?php

namespace App\Http\Controllers;

use App\Models\FamilyAuditLog;
use Illuminate\Support\Carbon;

class FamilyHistoryController extends Controller
{
    public function index()
    {
        $family = app('activeFamily');

        $logs = FamilyAuditLog::with(['actor'])
            ->where('family_id', $family->id)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $logsByDay = $logs->groupBy(fn ($log) =>
        $log->created_at->format('Y-m-d')
        );

        // фиксируем момент визита
        session(['family_history_last_seen' => now()]);

        return view('family.history', compact(
            'family',
            'logsByDay'
        ));
    }
}
session([
    'family_history_last_seen' => now(),
]);
