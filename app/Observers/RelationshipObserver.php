<?php

namespace App\Observers;

use App\Models\Relationship;
use App\Models\FamilyAuditLog;

class RelationshipObserver
{
    public function created(Relationship $relation): void
    {
        FamilyAuditLog::create([
            'family_id'     => $relation->family_id,
            'actor_user_id' => auth()->id(),
            'action'        => 'relation_created',
            'meta' => [
                'parent_name' => $relation->parent?->full_name,
                'child_name'  => $relation->child?->full_name,
            ],
        ]);
    }

    public function deleted(Relationship $relation): void
    {
        FamilyAuditLog::create([
            'family_id'     => $relation->family_id,
            'actor_user_id' => auth()->id(),
            'action'        => 'relation_deleted',
            'meta' => [
                'parent_name' => $relation->parent?->full_name,
                'child_name'  => $relation->child?->full_name,
            ],
        ]);
    }
}
