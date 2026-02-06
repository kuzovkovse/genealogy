<?php

namespace App\Http\Controllers;

use App\Models\PersonMilitaryService;
use App\Models\PersonMilitaryDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonMilitaryDocumentController extends Controller
{
    /* ===============================
     * ➕ Загрузка документа
     * =============================== */
    public function store(Request $request, PersonMilitaryService $service)
    {
        $person = $service->person;
        $this->authorizePerson($person);

        $data = $request->validate([
            'file'          => 'required|file|max:10240', // 10 МБ
            'title'         => 'nullable|string|max:255',
            'document_date' => 'nullable|date',
        ]);

        $file = $request->file('file');

        $mime = $file->getMimeType();
        $type = str_starts_with($mime, 'image/')
            ? 'image'
            : 'pdf';

        $path = $file->store(
            'military-documents/' . $service->id,
            'public'
        );

        $service->documents()->create([
            'type'          => $type,
            'title'         => $data['title'] ?? $file->getClientOriginalName(),
            'document_date' => $data['document_date'] ?? null,
            'file_path'     => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $mime,
            'size'          => $file->getSize(),
        ]);

        return back()->with('success', 'Документ добавлен');
    }

    /* ===============================
     * 🗑 Удаление документа
     * =============================== */
    public function destroy(PersonMilitaryDocument $document)
    {
        $service = $document->service;
        $person  = $service->person;

        $this->authorizePerson($person);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Документ удалён');
    }
}
