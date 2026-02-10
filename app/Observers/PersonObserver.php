<?php

namespace App\Observers;

use App\Models\Person;
use App\Models\FamilyAuditLog;
use Illuminate\Support\Facades\Auth;

class PersonObserver
{
    public function created(Person $person): void
    {
        $this->log('person_created', $person);
    }

    public function updated(Person $person): void
    {
        $changes = [];

        foreach ($person->getChanges() as $field => $newValue) {
            if ($field === 'updated_at') {
                continue;
            }

            $oldValue = $person->getOriginal($field);

            if ($oldValue == $newValue) {
                continue;
            }

            $changes[$field] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }

        // ❗ Если реально ничего значимого не поменялось — не логируем
        if (empty($changes)) {
            return;
        }

        FamilyAuditLog::create([
            'family_id'     => $person->family_id,
            'actor_user_id' => Auth::id(),
            'action'        => 'person_updated',
            'meta'          => [
                'person_id'        => $person->id,
                'person_name'      => $person->full_name, // ✅ теперь работает
                'person_full_name' => $person->full_name, // на будущее
                'changes'          => $changes,
            ],
        ]);
    }

    public function deleted(Person $person): void
    {
        $this->log('person_deleted', $person);
    }

    protected function log(string $action, Person $person): void
    {
        FamilyAuditLog::create([
            'family_id'     => $person->family_id,
            'actor_user_id' => Auth::id(),
            'action'        => $action,
            'meta'          => [
                'person_id'        => $person->id,
                'person_name'      => $person->full_name,
                'person_full_name' => $person->full_name,
            ],
        ]);
    }
}
