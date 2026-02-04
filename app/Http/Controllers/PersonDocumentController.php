<?php
namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonDocument;
use Illuminate\Http\Request;

class PersonDocumentController extends Controller
{
    public function store(Request $request, Person $person)
    {
        $data = $request->validate([
            'title'       => 'nullable|string|max:255',
            'type'        => 'nullable|string|max:100',
            'year'        => 'nullable|integer',
            'description' => 'nullable|string',
            'file'        => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        PersonDocument::create([
            'person_id'  => $person->id,
            'title'      => $data['title'],
            'type'       => $data['type'],
            'year'       => $data['year'],
            'description'=> $data['description'],
            'file_path'  => $path,
        ]);

        return back();
    }

    public function destroy(PersonDocument $document)
    {
        $document->delete();
        return back();
    }
}
