<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonMilitaryService;
use App\Models\PersonMilitaryDocument;
use Illuminate\Http\Request;

class PersonMilitaryServiceController extends Controller
{
    /* ===============================
     * ➕ Добавление службы
     * =============================== */
    public function store(Request $request, Person $person)
    {
        $this->authorizePerson($person);

        $data = $this->validateData($request);
        $data['is_killed'] = $request->boolean('is_killed');

        $person->militaryServices()->create($data);

        return back()->with('success', 'Запись военной службы добавлена');
    }

    /* ===============================
     * ✏️ Обновление службы
     * =============================== */
    public function update(Request $request, PersonMilitaryService $service)
    {
        $person = $service->person;
        $this->authorizePerson($person);

        $data = $this->validateData($request);
        $data['is_killed'] = $request->boolean('is_killed');

        $service->update($data);

        return back()->with('success', 'Запись военной службы обновлена');
    }

    /* ===============================
     * 🗑 Удаление службы
     * =============================== */
    public function destroy(PersonMilitaryService $service)
    {
        $person = $service->person;
        $this->authorizePerson($person);

        $service->delete();

        return back()->with('success', 'Запись военной службы удалена');
    }


    protected function authorizePerson(\App\Models\Person $person): void
    {
        $family = app('activeFamily');

        if (!$family || $person->family_id !== $family->id) {
            abort(403, 'Нет доступа к человеку');
        }
    }

    /* ===============================
     * ✅ ВАЛИДАЦИЯ
     * =============================== */
    protected function validateData(Request $request): array
    {
        $data = $request->validate([
            'war_type'     => 'required|string|max:255',
            'rank'         => 'nullable|string|max:255',
            'unit'         => 'nullable|string|max:255',
            'draft_year'   => 'nullable|integer|min:1800|max:' . date('Y'),
            'service_end'  => 'nullable|integer|min:1800|max:' . date('Y'),
            'awards'       => 'nullable|string',

            'killed_date'  => 'nullable|date',
            'burial_place' => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        // 🔥 ВОТ ЭТО ДОБАВЛЯЕМ
        if (!empty($data['service_end'])) {
            $data['service_end'] = $data['service_end'] . '-01-01';
        }

        return $data;
    }


}
