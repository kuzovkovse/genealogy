<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonEvent;
use Illuminate\Http\Request;

class PersonEventController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'event_date'  => 'required|date',
            'type'        => 'required|string',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
        ]);

        if (empty($data['icon'])) {
            $data['icon'] = PersonEvent::TYPES[$data['type']]['icon'] ?? '📌';
        }

        $person->events()->create([
            ...$data,
            'is_system' => false,
        ]);

        return back()->with('success', 'Событие добавлено');
    }

    public function update(Request $request, Person $person, PersonEvent $event)
    {
        if ($event->is_system) {
            abort(403);
        }

        $data = $request->validate([
            'event_date'  => 'required|date',
            'type'        => 'required|string',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
        ]);

        if (empty($data['icon'])) {
            $data['icon'] = PersonEvent::TYPES[$data['type']]['icon'] ?? '📌';
        }

        $event->update($data);

        return back()->with('success', 'Событие обновлено');
    }

    public function destroy(Person $person, PersonEvent $event)
    {
        if ($event->is_system) {
            abort(403);
        }

        $event->delete();

        return back()->with('success', 'Событие удалено');
    }
}
