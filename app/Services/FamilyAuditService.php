<?php

namespace App\Services;

use App\Models\FamilyAuditLog;

class FamilyAuditService
{
    public function log(
        int $familyId,
        int $actorUserId,
        string $action,
        ?int $targetUserId = null,
        array $meta = []
    ): void {
        FamilyAuditLog::create([
            'family_id'       => $familyId,
            'actor_user_id'   => $actorUserId,
            'target_user_id'  => $targetUserId,
            'action'          => $action,
            'meta'            => $meta,
        ]);
    }
}
