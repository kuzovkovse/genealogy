<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonEventController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'event_date'  => 'required|date',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
        ]);

        $person->events()->create([
            ...$data,
            'type' => 'custom',
            'is_system' => false,
        ]);

        return back()->with('success', 'Событие добавлено');
    }
    public function update(Request $request, Person $person, $eventId)
    {
        $event = $person->events()->findOrFail($eventId);

        if ($event->is_system) {
            abort(403);
        }

        $data = $request->validate([
            'event_date'  => 'required|date',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
        ]);

        $event->update($data);

        return back()->with('success', 'Событие обновлено');
    }
    public function destroy(Person $person, $eventId)
    {
        $event = $person->events()->findOrFail($eventId);

        if ($event->is_system) {
            abort(403);
        }

        $event->delete();

        return back()->with('success', 'Событие удалено');
    }
}
