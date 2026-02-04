<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Person;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'event_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        $person->events()->create([
            ...$data,
            'is_system' => false,
        ]);

        return back();
    }

    public function update(Request $request, Event $event)
    {
        abort_if($event->is_system, 403);

        $data = $request->validate([
            'event_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        $event->update($data);

        return back();
    }

    public function destroy(Event $event)
    {
        abort_if($event->is_system, 403);

        $event->delete();

        return back();
    }
}
