<?php

namespace App\Services;

use App\Models\Person;
use App\Models\FamilyAuditLog;
use Illuminate\Support\Facades\DB;

class PersonMergeService
{
    public function merge(Person $source, Person $target): void
    {
        DB::transaction(function () use ($source, $target) {

            // 👉 тут твоя реальная логика переноса данных / связей

            $source->delete();

            FamilyAuditLog::create([
                'family_id'     => $target->family_id,
                'actor_user_id' => auth()->id(),
                'action'        => 'person_merged',
                'meta' => [
                    'source_name' => $source->full_name,
                    'target_name' => $target->full_name,
                ],
            ]);
        });
    }
}
