<style>
    .marriages {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .family-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(0,0,0,.05);
        margin-bottom: 32px;
    }

    .family-subtitle {
        font-size: 14px;
        color: #6b7280;
        font-style: italic;
        margin-top: 4px;
    }

    .marriage-card {
        background: #fff;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(0,0,0,.05);
        border-left: 6px solid transparent;
        transition: background .2s ease, opacity .2s ease;
    }

    .relation-marriage { border-left-color: #f59e0b; }
    .relation-civil    { border-left-color: #6366f1; }
    .relation-parents  { border-left-color: #06b6d4; }

    .marriage-active {
        background: linear-gradient(to right, #f0fdf4, #ffffff);
    }

    .marriage-ended {
        background: #f9fafb;
        opacity: .92;
        border-left-color: #9ca3af !important;
    }

    .badge-status {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 999px;
        margin-left: 8px;
    }

    .badge-active {
        background: #dcfce7;
        color: #166534;
    }

    .badge-divorce {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-death {
        background: #e5e7eb;
        color: #374151;
    }

    .marriage-title {
        font-weight: 600;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .marriage-period {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }

    .spouse-card {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 10px;
        border-radius: 12px;
        background: #f9fafb;
        margin-bottom: 12px;
    }

    .spouse-photo {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .children {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .child-card {
        width: 90px;
        text-align: center;
        padding: 8px 6px;
        border-radius: 12px;
        background: #f3f4f6;
        cursor: pointer;
        transition: transform .15s ease, background .15s ease;
        position: relative;
    }

    .child-card:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
    }

    .child-photo {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 4px;
        border: 2px solid #e5e7eb;
    }

    .child-name {
        font-size: 13px;
        font-weight: 500;
    }

    .child-role {
        font-size: 11px;
        color: #6b7280;
    }

    .add-child-box {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #d1d5db;
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

                    $startDate = $couple->started_at ?? $couple->start_date ?? null;
                    $endDate   = $couple->ended_at ?? $couple->end_date ?? $couple->divorced_at ?? null;

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
                            $durationYears = $start->diffInYears($end);
                        }
                    }
                @endphp

                <div class="marriage-card {{ $relation['class'] }} {{ $isEnded ? 'marriage-ended' : 'marriage-active' }}">

                    <div class="marriage-title">
                        {{ $relation['icon'] }} {{ $relation['label'] }}

                        @if(!$isEnded)
                            <span class="badge-status badge-active">Действующий</span>
                        @elseif($endedByDeath)
                            <span class="badge-status badge-death">Завершён</span>
                        @else
                            <span class="badge-status badge-divorce">Развод</span>
                        @endif
                    </div>

                    @if($startDate || $endDate)
                        <div class="marriage-period">
                            {{ $startDate ? Carbon::parse($startDate)->year : '?' }}
                            —
                            {{ $endDate ? Carbon::parse($endDate)->year : 'н.в.' }}
                        </div>

                        @if($durationYears)
                            <div class="small text-muted">
                                В браке {{ $durationYears }} {{ \Illuminate\Support\Str::plural('год', $durationYears) }}
                            </div>
                        @endif

                        @if($endedByDeath)
                            <div class="small text-muted" style="font-style: italic;">
                                🕯 Брак длился до ухода супруга в {{ Carbon::parse($endDate)->year }} году
                            </div>
                        @endif

                    @endif

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
                        <button class="btn btn-sm btn-link text-muted p-0 mt-2"
                                onclick="toggleAddChild({{ $couple->id }})">
                            ➕ Добавить ребёнка в эту семью
                        </button>

                        <div class="add-child-box d-none" id="add-child-box-{{ $couple->id }}">

                            <form method="POST"
                                  action="{{ route('couples.children.store', $couple) }}"
                                  class="mb-2">
                                @csrf
                                <div class="d-flex gap-2">
                                    <input name="first_name" class="form-control form-control-sm" placeholder="Имя" required>
                                    <input name="last_name" class="form-control form-control-sm" placeholder="Фамилия">
                                    <button class="btn btn-sm btn-outline-primary">➕</button>
                                </div>
                            </form>

                            @if(isset($existingChildrenCandidates) && $existingChildrenCandidates->count())
                                <form method="POST"
                                      action="{{ route('couples.children.attach', $couple) }}">
                                    @csrf
                                    <div class="d-flex gap-2">
                                        <select name="child_id" class="form-select form-select-sm" required>
                                            <option value="">Выбрать существующего ребёнка</option>
                                            @foreach($existingChildrenCandidates as $candidate)
                                                <option value="{{ $candidate->id }}">
                                                    {{ $candidate->last_name }}
                                                    {{ $candidate->first_name }}
                                                    {{ $candidate->patronymic }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-secondary">🔗</button>
                                    </div>
                                </form>
                            @endif

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
