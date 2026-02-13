<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Couple;
use Illuminate\Http\Request;
use App\Services\FamilyContext;

class CoupleController extends Controller
{
    /**
     * Создание связи (брак / союз / родители)
     */
    public function store(Request $request, Person $person)
    {
        // 🔐 Права (owner / editor)
        $this->authorize('create', Couple::class);

        $family = FamilyContext::require();

        $data = $request->validate([
            'spouse_id'     => 'required|exists:people,id',
            'relation_type' => 'required|in:marriage,civil,parents',
            'married_at'    => 'nullable|date',
            'divorced_at'   => 'nullable|date',
        ]);

        // 🚫 Нельзя связать человека с самим собой
        if ((int) $data['spouse_id'] === (int) $person->id) {
            return back()->withErrors([
                'spouse_id' => 'Нельзя создать связь с самим собой',
            ]);
        }

        $spouse = Person::findOrFail($data['spouse_id']);

        // 🛡 Защита: оба человека должны быть из одной семьи
        if (
            $person->family_id !== $family->id
            || $spouse->family_id !== $family->id
        ) {
            abort(403);
        }

        // 🛡 Защита от дублирующей связи
        $exists = Couple::where(function ($q) use ($person, $spouse) {
            $q->where('person_1_id', $person->id)
                ->where('person_2_id', $spouse->id);
        })->orWhere(function ($q) use ($person, $spouse) {
            $q->where('person_1_id', $spouse->id)
                ->where('person_2_id', $person->id);
        })->exists();

        if ($exists) {
            return back()->withErrors([
                'spouse_id' => 'Связь между этими людьми уже существует',
            ]);
        }

        // 👶 Для "родителей" — даты не имеют смысла
        if ($data['relation_type'] === 'parents') {
            $data['married_at']  = null;
            $data['divorced_at'] = null;
        }

        Couple::create([
            'family_id'     => $family->id,
            'person_1_id'   => $person->id,
            'person_2_id'   => $spouse->id,
            'relation_type' => $data['relation_type'],
            'married_at'    => $data['married_at'] ?? null,
            'divorced_at'   => $data['divorced_at'] ?? null,
        ]);

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'Связь успешно добавлена');
    }

    public function edit(Couple $couple)
    {
        $this->authorize('update', $couple);

        return view('couples.edit', compact('couple'));
    }

    public function update(Request $request, Couple $couple)
    {
        $validated = $request->validate([
            'relation_type' => 'required|string',
            'started_at'    => 'nullable|string',
            'ended_at'      => 'nullable|string',
        ]);

        $startedAt = null;
        $endedAt   = null;

        if (!empty($validated['started_at'])) {
            $startedAt = \Carbon\Carbon::createFromFormat('d.m.Y', $validated['started_at'])
                ->format('Y-m-d');
        }

        if (!empty($validated['ended_at'])) {
            $endedAt = \Carbon\Carbon::createFromFormat('d.m.Y', $validated['ended_at'])
                ->format('Y-m-d');
        }

        $couple->update([
            'relation_type' => $validated['relation_type'],
            'married_at'    => $startedAt,
            'divorced_at'   => $endedAt,
        ]);

        return redirect()
            ->route('people.show', $couple->person_1_id)
            ->with('success', 'Связь обновлена');
    }

}
