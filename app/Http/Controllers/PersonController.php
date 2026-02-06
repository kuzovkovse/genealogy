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



class PersonController extends Controller
{
    /* ===============================
     * Список людей
     * =============================== */
    public function index()
    {
        $family = FamilyContext::require();

        $people = Person::where('family_id', $family->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('people.index', compact('people'));
    }

    /* ===============================
     * Создание
     * =============================== */
    public function create()
    {
        return view('people.create');
    }

    public function store(Request $request)
    {
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

        // 💡 Автологика: девичья фамилия
        if (
            ($data['gender'] ?? null) === 'female'
            && empty($data['birth_last_name'])
            && !empty($data['last_name'])
        ) {
            $data['birth_last_name'] = $data['last_name'];
        }


        if (($data['birth_date'] ?? '') === '') {
            $data['birth_date'] = null;
        }

        if (($data['death_date'] ?? '') === '') {
            $data['death_date'] = null;
        }

        $data['family_id'] = $family->id;
        $data['photo'] = null;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('people', 'public');
        }

        $person = Person::create($data);

        return redirect()->route('tree.view', $person);
    }

    /* ===============================
     * Карточка человека
     * =============================== */
    public function show(Person $person)
    {
        $this->authorizePerson($person);

        $familyId = FamilyContext::require()->id;
        $couples = $person->couples;

        // 👥 Кандидаты для создания связи
        $marriageCandidates = Person::where('family_id', $familyId)
            ->where('id', '!=', $person->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // 👶 Кандидаты в дети (существующие)
        $existingChildrenCandidates = Person::where('family_id', $familyId)
            ->whereNull('couple_id')          // ещё не в браке
            ->where('id', '!=', $person->id)  // не сам
            ->orderBy('birth_date')
            ->get();

        /* ---------- РОДИТЕЛИ ---------- */
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

        /* ---------- ДЕДЫ / БАБУШКИ ---------- */
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

        /* ---------- БРАТЬЯ / СЁСТРЫ ---------- */
        $siblings = collect();
        if ($person->couple_id) {
            $siblings = Person::where('couple_id', $person->couple_id)
                ->where('id', '!=', $person->id)
                ->where('family_id', $familyId)
                ->get();
        }

        /* ---------- СВОДНЫЕ ---------- */
        $halfSiblingsFather = collect();
        $halfSiblingsMother = collect();

        if ($father) {
            $fatherCouples = Couple::where(function ($q) use ($father) {
                $q->where('person_1_id', $father->id)
                    ->orWhere('person_2_id', $father->id);
            })
                ->where('id', '!=', $person->couple_id)
                ->with('children')
                ->get();

            foreach ($fatherCouples as $c) {
                $halfSiblingsFather = $halfSiblingsFather->merge(
                    $c->children->where('family_id', $familyId)
                );
            }
        }

        if ($mother) {
            $motherCouples = Couple::where(function ($q) use ($mother) {
                $q->where('person_1_id', $mother->id)
                    ->orWhere('person_2_id', $mother->id);
            })
                ->where('id', '!=', $person->couple_id)
                ->with('children')
                ->get();

            foreach ($motherCouples as $c) {
                $halfSiblingsMother = $halfSiblingsMother->merge(
                    $c->children->where('family_id', $familyId)
                );
            }
        }

        $halfSiblingsFather = $halfSiblingsFather->where('id', '!=', $person->id)->unique('id')->values();
        $halfSiblingsMother = $halfSiblingsMother->where('id', '!=', $person->id)->unique('id')->values();

        /* ---------- ДЕТИ ---------- */
        $childrenByCouple = [];
        foreach ($couples as $couple) {
            $childrenByCouple[$couple->id] =
                $couple->children->where('family_id', $familyId);
        }

        /* ---------- ХРОНОЛОГИЯ ---------- */
        $timeline = collect();

        /* 🔹 системные события */
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

        foreach ($couples as $c) {
            if ($c->married_at) {
                $timeline->push([
                    'event_date' => $c->married_at,
                    'title' => 'Брак',
                    'description' => null,
                    'icon' => '💍',
                    'is_system' => true,
                    'model' => null,
                ]);
            }
        }

        foreach ($couples as $couple) {
            foreach ($couple->children as $child) {
                if ($child->birth_date) {
                    $timeline->push([
                        'event_date' => $child->birth_date,
                        'title' => 'Рождение ' . ($child->gender === 'female' ? 'дочери' : 'сына'),
                        'description' => $child->full_name,
                        'icon' => '👶',
                        'is_system' => true,
                        'model' => null,
                    ]);
                }
            }
        }


        /* ---------- ВОЕННАЯ СЛУЖБА ---------- */
        foreach ($person->militaryServices as $service) {

            // 🪖 Призыв
            if ($service->draft_year) {
                $timeline->push([
                    'event_date' => Carbon::create($service->draft_year, 1, 1)->toDateString(),
                    'title'      => 'Призван на военную службу',
                    'description'=> trim(
                        $service->warLabel()
                        . ($service->unit ? ', ' . $service->unit : '')
                    ),
                    'icon'       => '🪖',
                    'is_system'  => true,
                    'model'      => null,
                ]);
            }

            // 🎖 Окончание службы
            if ($service->service_end) {
                $timeline->push([
                    'event_date' => Carbon::create($service->service_end, 12, 31)->toDateString(),
                    'title'      => 'Окончание военной службы',
                    'description'=> $service->warLabel(),
                    'icon'       => '🎖',
                    'is_system'  => true,
                    'model'      => null,
                ]);
            }

            // ✝ Гибель
            if ($service->is_killed && $service->killed_date) {
                $timeline->push([
                    'event_date' => $service->killed_date,
                    'title'      => 'Погиб в ходе службы',
                    'description'=> $service->warLabel(),
                    'icon'       => '✝',
                    'is_system'  => true,
                    'model'      => null,
                ]);
            }
        }


        /* 🔹 пользовательские события */
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

        /* 🔹 сортировка */
        $timeline = $timeline
            ->sortBy('event_date')
            ->values();

        $activeCandlesCount = $person->activeCandles()->count();
        $lastCandles = $person->memorialCandles()->latest('lit_at')->take(5)->get();

        // ================= РОДСТВО (НОВОЕ) =================
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
            'halfSiblingsFather',
            'halfSiblingsMother',
            'childrenByCouple',
            'timeline',
            'activeCandlesCount',
            'lastCandles',
            'marriageCandidates',
            'existingChildrenCandidates',
            'kinship'
        ));
    }

    /* ===============================
     * Редактирование
     * =============================== */
    public function edit(Person $person)
    {
        $this->authorizePerson($person);
        return view('people.edit', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        $this->authorizePerson($person);

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

// 💡 Автологика: девичья фамилия
        if (
            ($data['gender'] ?? null) === 'female'
            && empty($data['birth_last_name'])
            && !empty($data['last_name'])
        ) {
            $data['birth_last_name'] = $data['last_name'];
        }

        if (($data['birth_date'] ?? '') === '') {
            $data['birth_date'] = null;
        }

        if (($data['death_date'] ?? '') === '') {
            $data['death_date'] = null;
        }
        $data['is_war_participant'] = $request->boolean('is_war_participant');
        $person->update($data);

        return redirect()->route('people.show', $person);
    }

    /* ===============================
     * Фото человека
     * =============================== */
    public function updatePhoto(Request $request, Person $person)
    {
        $this->authorizePerson($person);

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
     * Место памяти
     * =============================== */
    public function updateMemorial(Request $request, Person $person)
    {
        $this->authorizePerson($person);

        if (!$person->death_date) {
            abort(403);
        }

        $data = $request->validate([
            'burial_cemetery'    => 'nullable|string|max:255',
            'burial_city'        => 'nullable|string|max:255',
            'burial_place'       => 'nullable|string|max:255',
            'burial_description' => 'nullable|string',
            'burial_lat'         => 'nullable|numeric',
            'burial_lng'         => 'nullable|numeric',
        ]);

        $person->update($data);

        return back()->with('success', 'Место памяти сохранено');
    }

    /* ===============================
     * Фото памяти
     * =============================== */
    public function storeMemorialPhoto(Request $request, Person $person)
    {
        $this->authorizePerson($person);

        if (!$person->death_date) {
            abort(403);
        }

        $data = $request->validate([
            'photo' => 'required|image|max:4096',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'taken_year' => 'nullable|integer|min:1800|max:' . date('Y'),
        ]);

        $path = $request->file('photo')->store('memorials', 'public');

        MemorialPhoto::create([
            'person_id'   => $person->id,
            'image_path'  => $path,
            'title'       => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'taken_year'  => $data['taken_year'] ?? null,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Фото добавлено');
    }

    /* ===============================
     * Свеча
     * =============================== */
    public function lightCandle(Person $person)
    {
        $this->authorizePerson($person);

        // нельзя зажигать живому
        if (!$person->death_date) {
            abort(403);
        }

        $userId = auth()->id();

        // ⏳ 1. Ограничение по времени (12 часов)
        $lastCandle = MemorialCandle::where('person_id', $person->id)
            ->where('user_id', $userId)
            ->latest('lit_at')
            ->first();

        if ($lastCandle && $lastCandle->lit_at->gt(now()->subHours(12))) {
            return back()->with('error', 'Вы уже зажигали свечу недавно 🙏');
        }

        // 🔥 2. Ограничение по количеству активных свечей
        $activeCount = MemorialCandle::where('person_id', $person->id)
            ->where('user_id', $userId)
            ->where('lit_at', '>=', now()->subDays(3)) // свеча «горит» 3 дня
            ->count();

        if ($activeCount >= 3) {
            return back()->with('error', 'Слишком много зажжённых свечей 🙏');
        }

        MemorialCandle::create([
            'person_id' => $person->id,
            'user_id'   => $userId,
            'lit_at'    => now(),
        ]);

        return back()->with('success', '🕯 Свеча зажжена');
    }

    /* ===============================
 * 📖 ИСТОРИЯ ЖИЗНИ
 * =============================== */
    public function updateBiography(Request $request, Person $person)
    {
        $this->authorizePerson($person);

        $data = $request->validate([
            'biography' => ['nullable', 'string'],
        ]);

        $person->update([
            'biography' => $data['biography'],
        ]);

        return back()->with('success', 'История жизни сохранена');
    }

    /* ===============================
   * Удаление фото из галереи
   * =============================== */

    public function destroyGalleryPhoto(Person $person, PersonPhoto $photo)
    {
        $this->authorizePerson($person);

        // защита от подмены
        if ($photo->person_id !== $person->id) {
            abort(403);
        }

        if ($photo->image_path) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $photo->delete();

        return back()->with('success', 'Фото удалено');
    }

    /* ===============================
     * Защита
     * =============================== */

}
