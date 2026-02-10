<?php

namespace App\Services;

use App\Models\FamilyAuditLog;
use Carbon\Carbon;

class FamilyNotificationsService
{
    public static function recent(int $limit = 5)
    {
        $lastSeen = session('family_history_last_seen');

        return FamilyAuditLog::query()
            ->where('family_id', FamilyContext::id())
            ->when($lastSeen, fn ($q) =>
            $q->where('created_at', '>', $lastSeen)
            )
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
