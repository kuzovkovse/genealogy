<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FamilyContext;

class PersonPhotoController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'photo'       => 'required|image|max:4096',
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'taken_year'  => 'nullable|integer|min:1800|max:' . now()->year,
        ]);

        $path = $request->file('photo')->store('people/photos', 'public');

        PersonPhoto::create([
            'person_id'   => $person->id,
            'image_path'  => $path,
            'title'       => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'taken_year'  => $data['taken_year'] ?? null,
        ]);

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'Фото добавлено');
    }

    public function destroy(PersonPhoto $photo)
    {
        $family = FamilyContext::require();

        if (!$family || $photo->person->family_id !== $family->id) {
            abort(403, 'Нет доступа к фото');
        }

        if ($photo->image_path && Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $person = $photo->person;

        $photo->delete();

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'Фото удалено');
    }

}
