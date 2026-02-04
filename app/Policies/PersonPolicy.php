<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Person;

class PersonPolicy
{
    public function update(?User $user, Person $person): bool
    {
        return true;
    }
}
