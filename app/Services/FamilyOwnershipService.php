<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\FamilyAuditService;

class FamilyOwnershipService
{
    public function __construct(
        protected FamilyAuditService $audit
    ) {}

    /**
     * Controlled transfer of family ownership.
     *
     * @throws HttpException
     */
    public function transfer(int $familyId, int $newOwnerUserId): void
    {
        try {
            DB::statement(
                'CALL transfer_family_ownership(?, ?)',
                [$familyId, $newOwnerUserId]
            );
        } catch (\Illuminate\Database\QueryException $e) {

            if (
                isset($e->errorInfo[0]) &&
                $e->errorInfo[0] === '45000'
            ) {
                throw new HttpException(403, $e->getMessage());
            }

            throw $e;
        }

        // ✅ Audit-log ТОЛЬКО после успешного transfer
        $this->audit->log(
            familyId: $familyId,
            actorUserId: auth()->id(),
            action: 'transfer_ownership',
            targetUserId: $newOwnerUserId,
            meta: [
                'type' => 'ownership_transfer',
            ]
        );
    }
}
