<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Couple;
use Illuminate\Http\Request;

class CoupleController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'spouse_id'     => 'required|exists:people,id',
            'relation_type' => 'required|in:marriage,civil,parents',
            'married_at'    => 'nullable|date',
            'divorced_at'   => 'nullable|date',
        ]);

        // для родителей — даты не нужны
        if ($data['relation_type'] === 'parents') {
            $data['married_at'] = null;
            $data['divorced_at'] = null;
        }

        Couple::create([
            'person_1_id'   => $person->id,
            'person_2_id'   => $data['spouse_id'],
            'relation_type' => $data['relation_type'],
            'married_at'    => $data['married_at'],
            'divorced_at'   => $data['divorced_at'],
        ]);

        return redirect()->route('people.show', $person);
    }
}
