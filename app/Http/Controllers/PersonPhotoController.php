<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonPhotoController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'photo'       => 'required|image|max:4096',
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'year'        => 'nullable|integer|min:1800|max:' . now()->year,
        ]);

        $path = $request->file('photo')->store('people/photos', 'public');

        PersonPhoto::create([
            'person_id'   => $person->id,
            'image_path'  => $path,
            'title'       => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'year'        => $data['year'] ?? null,
        ]);

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'Фото добавлено');
    }
}
