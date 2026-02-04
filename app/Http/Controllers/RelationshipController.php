<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Relationship;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'related_person_id' => 'required|exists:people,id|different:person.id',
            'type' => 'required|in:father,mother,child,spouse',
        ]);

        $relatedPerson = Person::findOrFail($data['related_person_id']);

        // 1. Создаём основную связь
        Relationship::firstOrCreate([
            'person_id' => $person->id,
            'related_person_id' => $relatedPerson->id,
            'type' => $data['type'],
        ]);

        // 2. Определяем обратный тип
        $reverseType = match ($data['type']) {
            'father' => 'child',
            'mother' => 'child',
            'child'  => $person->gender === 'female' ? 'mother' : 'father',
            'spouse' => 'spouse',
        };

        // 3. Создаём обратную связь
        Relationship::firstOrCreate([
            'person_id' => $relatedPerson->id,
            'related_person_id' => $person->id,
            'type' => $reverseType,
        ]);

        return redirect()->route('people.show', $person);
    }

}
