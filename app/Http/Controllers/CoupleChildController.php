<?php

namespace App\Http\Controllers;

use App\Models\Couple;
use App\Models\Person;
use Illuminate\Http\Request;

class CoupleChildController extends Controller
{
    /**
     * ➕ Создать нового ребёнка
     */
    public function store(Request $request, Couple $couple)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|string|max:20',
        ]);

        Person::create([
            ...$data,
            'couple_id' => $couple->id,
        ]);

        return back()->with('success', 'Ребёнок добавлен');
    }

    /**
     * 🔗 Привязать существующего ребёнка
     */
    public function attach(Request $request, Couple $couple)
    {
        $data = $request->validate([
            'child_id' => 'required|exists:people,id',
        ]);

        $child = Person::findOrFail($data['child_id']);
        $child->couple_id = $couple->id;
        $child->save();

        return back()->with('success', 'Ребёнок привязан');
    }

    /**
     * 🗑 Отвязать ребёнка от брака
     */
    public function detach(Couple $couple, Person $child)
    {
        if ($child->couple_id === $couple->id) {
            $child->couple_id = null;
            $child->save();
        }

        return back()->with('success', 'Ребёнок отвязан');
    }
}
