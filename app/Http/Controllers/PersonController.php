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
    public function index(GenerationService $generationService)
    {
        $family = FamilyContext::require();

        // Все люди семьи
        $people = Person::where('family_id', $family->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Группировка по поколениям (I, II, III…)
        $generations = $generationService->build($people);

        return view('people.index', [
            'people'      => $people,      // оставляем для совместимости
            'generations' => $generations, // 👈 ОСНОВНОЕ
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
    public function show(Person $person)
    {
        /**
         * 🔐 Авторизация
         * FamilyContext::require() будет вызван внутри policy
         */
        $this->authorize('view', $person);

        $familyId = FamilyContext::require()->id;

        $couples = $person->couples;

        /* ---------- Кандидаты ---------- */

        // текущий человек
        $personId = $person->id;
        $personGender = $person->gender;

// 🔹 1. Все люди, которые уже состоят в любой паре
        $peopleInAnyCouple = Couple::query()
            ->select(['person_1_id', 'person_2_id'])
            ->get()
            ->flatMap(fn ($c) => [$c->person_1_id, $c->person_2_id])
            ->filter()
            ->unique()
            ->values()
            ->toArray();

// 🔹 2. Определяем допустимый пол партнёра
        $allowedGender = match ($personGender) {
            'male'   => 'female',
            'female' => 'male',
            default  => null, // если пол не указан — без фильтра
        };

// 🔹 3. Кандидаты в партнёры
        $marriageCandidates = Person::where('family_id', $familyId)
            ->where('id', '!=', $personId)              // не сам
            ->when($allowedGender, fn ($q) =>
            $q->where('gender', $allowedGender)     // противоположный пол
            )
            ->whereNotIn('id', $peopleInAnyCouple)      // только без пары
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();


        $existingChildrenCandidates = Person::where('family_id', $familyId)
            ->whereNull('couple_id')
            ->where('id', '!=', $person->id)
            ->orderBy('birth_date')
            ->get();

        /* ---------- Родители ---------- */

        $parentCouple = $person->couple_id
            ? Couple::with(['person1', 'person2'])->find($person->couple_id)
            : null;

        $father = null;
        $mother = null;

        if ($parentCouple) {
            foreach ([$parentCouple->person1, $parentCouple->person2] as $parent) {
                if (!$parent) continue;

                if ($parent->gender === 'male') {
                    $father = $parent;
                } elseif ($parent->gender === 'female') {
                    $mother = $parent;
                }
            }
        }

        /* ---------- Деды / бабушки ---------- */

        $grandparentsFather = collect();
        $grandparentsMother = collect();

        if ($father?->couple_id) {
            $fc = Couple::with(['person1', 'person2'])->find($father->couple_id);
            if ($fc?->person1) $grandparentsFather->push($fc->person1);
            if ($fc?->person2) $grandparentsFather->push($fc->person2);
        }

        if ($mother?->couple_id) {
            $mc = Couple::with(['person1', 'person2'])->find($mother->couple_id);
            if ($mc?->person1) $grandparentsMother->push($mc->person1);
            if ($mc?->person2) $grandparentsMother->push($mc->person2);
        }

        /* ---------- Братья / сёстры ---------- */

        $siblings = collect();

        if ($person->couple_id) {
            $siblings = Person::where('couple_id', $person->couple_id)
                ->where('id', '!=', $person->id)
                ->where('family_id', $familyId)
                ->get();
        }

        /* ---------- Хронология ---------- */

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

        $timeline = app(TimelineNarrativeService::class)
            ->enrich($timeline, $person);

        /* ---------- Сервисы ---------- */

        $nextSteps = app(NextStepService::class)->build($person, [
            'timeline_count' => $timeline->count(),
            'photos_count' => $person->photos()->count(),
            'military_services_count' => $person->militaryServices()->count(),
            'military_documents_count' => $person->militaryServices
                ->flatMap(fn ($s) => $s->documents)
                ->count(),
        ]);

        $memoryProgress = app(MemoryProgressService::class)->build($person);

        $activeCandlesCount = $person->activeCandles()->count();
        $lastCandles = $person->memorialCandles()->latest('lit_at')->take(5)->get();

        $todayInHistory = app(TodayInHistoryService::class)->build($person);
        $recentActivity = app(RecentActivityService::class)->build($person);

        /* ---------- Родство ---------- */

        $extended = request()->boolean('extended');
        $kinshipService = app(KinshipService::class);

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

        return view('people.show', compact(
            'person',
            'couples',
            'father',
            'mother',
            'grandparentsFather',
            'grandparentsMother',
            'siblings',
            'timeline',
            'activeCandlesCount',
            'lastCandles',
            'marriageCandidates',
            'existingChildrenCandidates',
            'kinship',
            'todayInHistory',
            'recentActivity',
            'nextSteps',
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
    /* ===============================
     * 🕯 Свеча памяти
     * =============================== */
    public function lightCandle(Request $request, Person $person)
    {
        if (!$person->death_date) {
            return response()->json([
                'ok' => false,
                'message' => 'Свечу можно зажечь только для умершего человека',
            ], 403);
        }

        $userId = auth()->id();

        $lastCandle = MemorialCandle::where('person_id', $person->id)
            ->where('user_id', $userId)
            ->latest('lit_at')
            ->first();

        if ($lastCandle && $lastCandle->lit_at->gt(now()->subHours(12))) {
            return response()->json([
                'ok' => false,
                'message' => 'Вы уже зажигали свечу недавно 🙏',
            ], 429);
        }

        MemorialCandle::create([
            'person_id' => $person->id,
            'user_id'   => $userId,
            'visitor_name' => $request->input('visitor_name'),
            'lit_at'    => now(),
        ]);

        return response()->json([
            'ok' => true,
            'active_count' => $person->activeCandlesCount(),
            'last_candles' => $person->memorialCandles()
                ->latest('lit_at')
                ->take(5)
                ->get()
                ->map(fn ($c) => [
                    'name' => $c->visitor_name ?? 'Аноним',
                    'time' => $c->lit_at?->locale('ru')->diffForHumans(),
                ]),
        ]);
    }


}
