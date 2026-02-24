<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Person;
use App\Models\Couple;
use App\Models\MemorialPhoto;
use App\Models\MemorialCandle;
use App\Services\FamilyContext;
use App\Services\KinshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PersonPhoto;
use App\Services\TimelineNarrativeService;
use App\Services\TodayInHistoryService;
use App\Services\RecentActivityService;
use App\Services\NextStepService;
use App\Services\MemoryProgressService;
use App\Services\GenerationService;


class PersonController extends Controller
{
    /* ===============================
  * 👥 Список людей (по поколениям)
  * =============================== */
    public function index(Request $request, GenerationService $generationService)
    {
        $mode = $request->query('mode', 'structure');

        $people = Person::query()
            ->where('family_id', app('activeFamily')->id)
            ->get();

        if ($mode === 'list') {

            // 📋 Общий список без поколений
            return view('people.index', [
                'mode' => $mode,
                'peopleList' => $people->sortBy('last_name'),
                'generations' => [],
            ]);
        }

        if ($mode === 'blood') {
            $generations = $generationService->buildBloodOnly($people);
        } else {
            // structure по умолчанию
            $generations = $generationService->buildWithSpouses($people);
        }

        /*
        |--------------------------------------------------------------------------
        | 👑 Определяем родоначальника
        |--------------------------------------------------------------------------
        */

        $rootId = $generationService->getRootPersonId($people);

        /*
|--------------------------------------------------------------------------
| 🧬 Глубина рода (эмоциональный слой)
|--------------------------------------------------------------------------
*/

        $allPeople = collect($generations)->flatten();

        $oldestBirth = $allPeople
            ->whereNotNull('birth_date')
            ->sortBy('birth_date')
            ->first();

        $yearsSpan = null;

        if ($oldestBirth && $oldestBirth->birth_date) {
            $yearsSpan = (int) Carbon::parse($oldestBirth->birth_date)
                ->diffInYears(now());
        }

        $totalGenerations = count($generations);

        return view('people.index', [
            'mode' => $mode,
            'generations' => $generations,
            'peopleList' => collect(),
            'rootId' => $rootId,   // ← ВОТ ЭТО ГЛАВНОЕ
            'yearsSpan' => $yearsSpan,
            'totalGenerations' => $totalGenerations,
        ]);
    }




    /* ===============================
     * ➕ Создание
     * =============================== */
    public function create()
    {
        $this->authorize('create', Person::class);

        return view('people.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Person::class);

        $family = FamilyContext::require();

        $data = $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'nullable|string|max:255',
            'birth_last_name'  => 'nullable|string|max:255',
            'patronymic'       => 'nullable|string|max:255',
            'gender'           => 'nullable|in:male,female',
            'birth_date'       => 'nullable|string|max:20',
            'death_date'       => 'nullable|string|max:20',
            'birth_place'      => 'nullable|string|max:255',
            'biography'        => 'nullable|string',
        ]);

        // 💡 автологика: девичья фамилия
        if (
            ($data['gender'] ?? null) === 'female'
            && empty($data['birth_last_name'])
            && !empty($data['last_name'])
        ) {
            $data['birth_last_name'] = $data['last_name'];
        }

        $data['birth_date'] = empty($data['birth_date']) ? null : $data['birth_date'];
        $data['death_date'] = empty($data['death_date']) ? null : $data['death_date'];
        $data['family_id']  = $family->id;
        $data['photo']      = null;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('people', 'public');
        }

        $person = Person::create($data);

        return redirect()->route('tree.view', $person);
    }

    /* ===============================
     * 👤 Карточка человека
     * =============================== */
    public function show(int $id)
    {
        $familyId = FamilyContext::require()->id;

        $person = Person::query()
            ->where('family_id', $familyId)
            ->with([
                'events',
                'photos',
                'memorialPhotos',
                'documents',

                'memorialCandles' => fn ($q) =>
                $q->latest('lit_at')->limit(5),

                'activeCandles',

                'militaryServices.documents',

                'parentCouple.person1.parentCouple.person1',
                'parentCouple.person1.parentCouple.person2',
                'parentCouple.person2.parentCouple.person1',
                'parentCouple.person2.parentCouple.person2',

                'couplesAsFirst.person2',
                'couplesAsSecond.person1',

                'children',
            ])
            ->withCount([
                'photos',
                'militaryServices',
                'activeCandles',
                'children',
            ])
            ->findOrFail($id);

        $this->authorize('view', $person);

        /* ===============================
           РОДИТЕЛИ
        =============================== */

        $father = null;
        $mother = null;

        if ($person->parentCouple) {
            foreach ([
                         $person->parentCouple->person1,
                         $person->parentCouple->person2
                     ] as $parent) {

                if (!$parent) continue;

                if ($parent->gender === 'male') $father = $parent;
                if ($parent->gender === 'female') $mother = $parent;
            }
        }

        $grandparentsFather = collect();
        $grandparentsMother = collect();

        if ($father?->parentCouple) {
            $grandparentsFather = collect([
                $father->parentCouple->person1,
                $father->parentCouple->person2,
            ])->filter();
        }

        if ($mother?->parentCouple) {
            $grandparentsMother = collect([
                $mother->parentCouple->person1,
                $mother->parentCouple->person2,
            ])->filter();
        }

        $siblings = collect();

        if ($person->couple_id) {
            $siblings = Person::where('family_id', $familyId)
                ->where('couple_id', $person->couple_id)
                ->where('id', '!=', $person->id)
                ->get();
        }

        /* ===============================
           ХРОНОЛОГИЯ
        =============================== */

        $timeline = collect();

        if ($person->birth_date) {
            $timeline->push([
                'event_date' => $person->birth_date,
                'title' => 'Рождение',
                'description' => null,
                'icon' => '🎂',
                'is_system' => true,
                'model' => null,
            ]);
        }

        foreach ($person->events as $event) {
            $timeline->push([
                'event_date' => $event->event_date,
                'title' => $event->title,
                'description' => $event->description,
                'icon' => $event->icon ?? '📌',
                'is_system' => false,
                'model' => $event,
            ]);
        }

        $timeline = $timeline->sortBy('event_date')->values();

        $timeline = app(\App\Services\TimelineNarrativeService::class)
            ->enrich($timeline, $person);

        /* ===============================
           СЕРВИСЫ
        =============================== */

        $nextSteps = app(\App\Services\NextStepService::class)->build($person, [
            'timeline_count' => $timeline->count(),
            'photos_count' => $person->photos_count,
            'military_services_count' => $person->military_services_count,
            'military_documents_count' => $person->militaryServices
                ->flatMap(fn ($s) => $s->documents)
                ->count(),
        ]);

        $memoryProgress = app(\App\Services\MemoryProgressService::class)->build($person);

        $activeCandlesCount = $person->active_candles_count;
        $lastCandles = $person->memorialCandles;

        $todayInHistory = app(\App\Services\TodayInHistoryService::class)->build($person);
        $recentActivity = app(\App\Services\RecentActivityService::class)->build($person);

        /* ===============================
           🧬 IN-MEMORY RODSTVO (БЕЗ SQL)
        =============================== */

        // 1️⃣ Загружаем всю семью (1 SQL)
        $familyPeople = Person::where('family_id', $familyId)->get();

        // 2️⃣ Передаём в новый сервис
        $kinshipService = new \App\Services\KinshipService($familyPeople);

        $extended = request()->boolean('extended');

        $kinship = (object) [
            'extended' => $extended,
            'siblings' => $kinshipService->getSiblings($person),
            'extendedSiblings' => $extended
                ? $kinshipService->getExtendedSiblings($person)
                : collect(),
            'ancestors' => $extended
                ? $kinshipService->getAncestors($person, 3)
                : collect(),
        ];

        /* ===============================
   КАНДИДАТЫ ДЛЯ СВЯЗИ
=============================== */

        $marriageCandidates = Person::query()
            ->where('family_id', $familyId)
            ->where('id', '!=', $person->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        /* ===============================
           VIEW
        =============================== */

        return view('people.show', compact(
            'person',
            'father',
            'mother',
            'grandparentsFather',
            'grandparentsMother',
            'siblings',
            'timeline',
            'activeCandlesCount',
            'lastCandles',
            'kinship',
            'todayInHistory',
            'recentActivity',
            'nextSteps',
            'memoryProgress',
            'marriageCandidates',
            'memoryProgress'
        ));
    }


    /* ===============================
     * ✏️ Редактирование
     * =============================== */
    public function edit(Person $person)
    {
        $this->authorize('update', $person);

        return view('people.edit', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $data = $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'nullable|string|max:255',
            'patronymic'       => 'nullable|string|max:255',
            'birth_last_name'  => 'nullable|string|max:255',
            'gender'           => 'nullable|in:male,female',
            'birth_date'       => 'nullable|string|max:20',
            'death_date'       => 'nullable|string|max:20',
            'is_war_participant' => 'nullable|boolean',
        ]);

        if (
            ($data['gender'] ?? null) === 'female'
            && empty($data['birth_last_name'])
            && !empty($data['last_name'])
        ) {
            $data['birth_last_name'] = $data['last_name'];
        }

        $data['birth_date'] = empty($data['birth_date']) ? null : $data['birth_date'];
        $data['death_date'] = empty($data['death_date']) ? null : $data['death_date'];
        $data['is_war_participant'] = $request->boolean('is_war_participant');

        $person->update($data);

        return redirect()->route('people.show', $person);
    }

    /* ===============================
     * 📷 Фото человека
     * =============================== */
    public function updatePhoto(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        if ($person->photo) {
            Storage::disk('public')->delete($person->photo);
        }

        $path = $request->file('photo')->store('people', 'public');
        $person->update(['photo' => $path]);

        return back()->with('success', 'Фото обновлено');
    }

    /* ===============================
         * БИОГРАФИЯ
         * =============================== */
    public function updateBiography(Request $request, Person $person)
    {
        $this->authorize('update', $person);

        $data = $request->validate([
            'biography' => ['nullable', 'string'],
        ]);

        $person->update([
            'biography' => $data['biography'],
        ]);

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'История жизни обновлена');
    }


    /* ===============================
         * МЕСТО ЗАХОРОНЕНИЯ
         * =============================== */
    public function updateMemorial(Request $request, Person $person)
    {
        $data = $request->validate([
            'burial_place'        => ['nullable', 'string', 'max:255'],
            'burial_city'         => ['nullable', 'string', 'max:255'],
            'burial_cemetery'     => ['nullable', 'string', 'max:255'],
            'burial_description'  => ['nullable', 'string'],
            'burial_lat'          => ['nullable', 'numeric'],
            'burial_lng'          => ['nullable', 'numeric'],
        ]);

        $person->update($data);

        return back()->with('success', 'Место памяти обновлено');
    }
    public function destroy(Person $person)
    {
        $person->update([
            'burial_cemetery' => null,
            'burial_city' => null,
            'burial_place' => null,
            'burial_description' => null,
            'burial_lat' => null,
            'burial_lng' => null,
        ]);

        return back()->with('success', 'Место памяти удалено');
    }


    /* ===============================
             * ФОТО МЕСТА ЗАХОРОНЕНИЯ
             * =============================== */
    public function storeMemorialPhoto(Request $request, \App\Models\Person $person)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:5120'], // 5MB
            'title' => ['nullable', 'string', 'max:255'],
            'year'  => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
        ]);

        // сохраняем файл
        $path = $request->file('photo')->store('memorials', 'public');

        // если у тебя отдельная таблица memorial_photos
        $person->memorialPhotos()->create([
            'image_path' => $path,
            'title' => $request->title,
            'year' => $request->year,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Фото места памяти добавлено');
    }

}
