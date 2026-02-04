<?php

namespace App\Http\Controllers;

use App\Models\Person;

class PublicPersonController extends Controller
{
    public function show(string $uuid)
    {
        $person = Person::where('public_uuid', $uuid)->firstOrFail();

        $couples = $person->couples()
            ->with(['person1', 'person2', 'children'])
            ->get();

        $parentsCouple = $person->parentCouple;

        return view('people.public.show', compact(
            'person',
            'couples',
            'parentsCouple'
        ));
    }
}
