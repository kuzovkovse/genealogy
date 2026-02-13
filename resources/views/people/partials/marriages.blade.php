<style>
    .marriages {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .family-card {
        background: #fff;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 6px 18px rgba(0,0,0,.04);
        margin-bottom: 32px;
    }

    .family-subtitle {
        font-size: 14px;
        color: #6b7280;
        font-style: italic;
        margin-top: 4px;
    }

    /* ==============================
       КАРТОЧКА СВЯЗИ (Историческая)
    ===============================*/

    .marriage-card {
        background: linear-gradient(to bottom right, #ffffff, #fafafa);
        border-radius: 18px;
        padding: 16px 18px;
        border-left: 4px solid #d1d5db;
        transition: all .25s ease;
        position: relative;
    }

    .marriage-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,.06);
    }

    .relation-marriage { border-left-color: #d6b36b; }
    .relation-civil    { border-left-color: #6366f1; }
    .relation-parents  { border-left-color: #06b6d4; }

    .marriage-ended {
        opacity: .95;
    }

    .marriage-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 10px;
    }

    .marriage-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
    }

    .badge-status {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 999px;
    }

    .badge-active  { background: #dcfce7; color: #166534; }
    .badge-divorce { background: #fee2e2; color: #991b1b; }
    .badge-death   { background: #e5e7eb; color: #374151; }

    .marriage-meta {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* Мини таймлайн */

    .timeline {
        height: 4px;
        background: #e5e7eb;
        border-radius: 999px;
        margin-top: 8px;
        position: relative;
        overflow: hidden;
    }

    .timeline-bar {
        height: 100%;
        background: linear-gradient(to right, #c9a646, #e5d3a1);
        border-radius: 999px;
        width: 100%;
    }

    .timeline-ended {
        background: linear-gradient(to right, #9ca3af, #d1d5db);
    }

    .spouse-card {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 14px;
        padding: 8px 0;
    }

    .spouse-photo {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .children {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .child-card {
        width: 82px;
        text-align: center;
        padding: 6px;
        border-radius: 12px;
        background: #f3f4f6;
        transition: all .2s ease;
        cursor: pointer;
    }

    .child-card:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    .child-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 4px;
        border: 2px solid #e5e7eb;
    }

    .child-name {
        font-size: 12px;
        font-weight: 500;
    }

    .child-role {
        font-size: 10px;
        color: #6b7280;
    }

    .edit-link {
        font-size: 15px;
        text-decoration: none;
        color: #6b7280;
        transition: color .2s ease;
    }

    .edit-link:hover {
        color: #111827;
    }

    /* ===============================
   Subtle Grain Texture
================================ */

    .marriage-card {
        position: relative;
        overflow: hidden
    }

    .marriage-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.035; /* регулируй 0.02–0.06 */
        mix-blend-mode: multiply;
        background-image: url("data:image/svg+xml;utf8,\
<svg xmlns='http://www.w3.org/2000/svg' width='100%' height='100%'>\
<filter id='noise'>\
<feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/>\
</filter>\
<rect width='100%' height='100%' filter='url(%23noise)'/>\
</svg>");
        background-size: 200px 200px;
    }

</style>

@php
    use Carbon\Carbon;

    $relationMap = [
        'marriage' => ['icon' => '💍', 'label' => 'Официальный брак', 'class' => 'relation-marriage'],
        'civil'    => ['icon' => '🤝', 'label' => 'Гражданский союз', 'class' => 'relation-civil'],
        'parents'  => ['icon' => '👶', 'label' => 'Родители ребёнка', 'class' => 'relation-parents'],
    ];
@endphp

<div class="family-card">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">👨‍👩‍👧 Семья и дети</h3>
            <div class="family-subtitle">
                Здесь постепенно складывается семейная история — партнёры и дети.
            </div>
        </div>

        @can('create', \App\Models\Couple::class)
            <button class="btn btn-sm btn-outline-primary"
                    onclick="toggleRelationshipForm()">
                ➕ Добавить партнёра
            </button>
        @endcan
    </div>

    <div id="relationship-form-container" class="d-none mb-3">
        @include('people.partials.relationship-form')
    </div>

    @if($person->couples->isEmpty())
        <div class="text-muted">Пока здесь нет семейных связей.</div>
    @else

        <div class="marriages">
            @foreach($person->couples as $couple)

                @php
                    $relation = $relationMap[$couple->relation_type ?? 'marriage'];

                    $spouse = $couple->person_1_id === $person->id
                        ? $couple->person2
                        : $couple->person1;

                    $children = $couple->children
                        ->sortBy(fn($c) => $c->birth_date ?? '9999-12-31')
                        ->values();

                    $startDate = $couple->married_at ?? null;
                    $endDate   = $couple->divorced_at ?? null;

                    $endedByDeath = false;

                    if (!$endDate) {
                        if ($spouse?->death_date) {
                            $endDate = $spouse->death_date;
                            $endedByDeath = true;
                        } elseif ($person->death_date) {
                            $endDate = $person->death_date;
                            $endedByDeath = true;
                        }
                    }

                    $isEnded = !empty($endDate);

                    $durationYears = null;

                    if ($startDate && $endDate) {
                        $start = Carbon::parse($startDate);
                        $end   = Carbon::parse($endDate);

                        if ($end->greaterThan($start)) {
                            $durationYears = (int) $start->diffInYears($end);
                        }
                    }
                @endphp

                <div class="marriage-card {{ $relation['class'] }} {{ $isEnded ? 'marriage-ended' : '' }}">

                    <div class="marriage-header">

                        <div>
                            <div class="marriage-title">
                                {{ $relation['icon'] }} {{ $relation['label'] }}

                                @if(!$isEnded)
                                    <span class="badge-status badge-active">Действующий</span>
                                @elseif($endedByDeath)
                                    <span class="badge-status badge-death">Завершён</span>
                                @else
                                    <span class="badge-status badge-divorce">Развод</span>
                                @endif

                                @if($endedByDeath)
                                    <span class="text-muted small">
                                        — Брак длился до ухода супруга в {{ Carbon::parse($endDate)->year }} году
                                    </span>
                                @endif
                            </div>

                            @if($startDate || $endDate)
                                <div class="marriage-meta">
                                    {{ $startDate ? Carbon::parse($startDate)->year : '?' }}
                                    —
                                    {{ $endDate ? Carbon::parse($endDate)->year : 'н.в.' }}
                                    @if($durationYears)
                                        · {{ $durationYears }} лет вместе
                                    @endif
                                </div>

                                <div class="timeline">
                                    <div class="timeline-bar {{ $isEnded ? 'timeline-ended' : '' }}"></div>
                                </div>
                            @endif
                        </div>

                        @can('update', $couple)
                            <a href="{{ route('couples.edit', $couple) }}"
                               class="edit-link">
                                ✏
                            </a>
                        @endcan

                    </div>

                    @if($spouse)
                        <div class="spouse-card">
                            <img class="spouse-photo"
                                 src="{{ $spouse->photo
                                    ? asset('storage/'.$spouse->photo)
                                    : route('avatar', [
                                        'name' => mb_substr($spouse->first_name,0,1).mb_substr($spouse->last_name ?? '',0,1),
                                        'gender' => $spouse->gender
                                    ]) }}">
                            <div>
                                <strong>
                                    {{ $spouse->last_name }}
                                    {{ $spouse->first_name }}
                                    {{ $spouse->patronymic }}
                                </strong><br>
                                <small class="text-muted">
                                    {{ $spouse->birth_date ? Carbon::parse($spouse->birth_date)->year : '?' }}
                                    —
                                    {{ $spouse->death_date ? Carbon::parse($spouse->death_date)->year : 'н.в.' }}
                                </small>
                            </div>
                        </div>
                    @endif

                    @if($children->count())
                        <div class="children">
                            @foreach($children as $child)
                                <div class="child-card"
                                     onclick="window.location.href='{{ route('people.show', $child) }}'">
                                    <img class="child-photo"
                                         src="{{ $child->photo
                                            ? asset('storage/'.$child->photo)
                                            : route('avatar', [
                                                'name' => mb_substr($child->first_name,0,1).mb_substr($child->last_name ?? '',0,1),
                                                'gender' => $child->gender
                                            ]) }}">
                                    <div class="child-name">{{ $child->first_name }}</div>
                                    <div class="child-role">
                                        {{ $child->gender === 'male' ? 'Сын' : 'Дочь' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted small mt-2">
                            У этой семьи пока не указаны дети
                        </div>
                    @endif
                    @can('manageChildren', $couple)

                        <div class="add-child-section mt-3">

                            <button class="btn btn-sm btn-link text-muted p-0"
                                    onclick="toggleAddChild({{ $couple->id }})">
                                ➕ Добавить ребёнка в эту семью
                            </button>

                            <div class="add-child-box d-none mt-2"
                                 id="add-child-box-{{ $couple->id }}">

                                {{-- Создать нового --}}
                                <form method="POST"
                                      action="{{ route('couples.children.store', $couple) }}"
                                      class="mb-2">
                                    @csrf
                                    <div class="d-flex gap-2">
                                        <input name="first_name"
                                               class="form-control form-control-sm"
                                               placeholder="Имя"
                                               required>

                                        <input name="last_name"
                                               class="form-control form-control-sm"
                                               placeholder="Фамилия">

                                        <button class="btn btn-sm btn-outline-primary">
                                            ➕
                                        </button>
                                    </div>
                                </form>

                                {{-- Привязать существующего --}}
                                @if(isset($existingChildrenCandidates) && $existingChildrenCandidates->count())
                                    <form method="POST"
                                          action="{{ route('couples.children.attach', $couple) }}">
                                        @csrf
                                        <div class="d-flex gap-2">
                                            <select name="child_id"
                                                    class="form-select form-select-sm"
                                                    required>
                                                <option value="">Выбрать существующего ребёнка</option>
                                                @foreach($existingChildrenCandidates as $candidate)
                                                    <option value="{{ $candidate->id }}">
                                                        {{ $candidate->last_name }}
                                                        {{ $candidate->first_name }}
                                                        {{ $candidate->patronymic }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                🔗
                                            </button>
                                        </div>
                                    </form>
                                @endif

                            </div>
                        </div>

                    @endcan

                </div>

            @endforeach
        </div>
    @endif

    <script>
        function toggleRelationshipForm() {
            const el = document.getElementById('relationship-form-container');
            if (el) el.classList.toggle('d-none');
        }

        function toggleAddChild(id) {
            const el = document.getElementById('add-child-box-' + id);
            if (el) el.classList.toggle('d-none');
        }

    </script>

</div>
