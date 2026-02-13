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

    /* ===== СТАТУС БРАКА ===== */

    .marriage-active {
        background: linear-gradient(to right, #f0fdf4, #ffffff);
    }

    .marriage-ended {
        background: #f9fafb;
        opacity: 0.85;
        border-left-color: #9ca3af !important;
    }

    .marriage-ended .marriage-title {
        color: #6b7280;
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

    .badge-ended {
        background: #e5e7eb;
        color: #374151;
    }

    .marriage-header {
        margin-bottom: 12px;
    }

    .marriage-title {
        font-weight: 600;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .marriage-period {
        font-size: 12px;
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
        line-height: 1.1;
    }

    .child-role {
        font-size: 11px;
        color: #6b7280;
    }

    .child-remove {
        position: absolute;
        top: 4px;
        right: 4px;
    }

    .child-remove-btn {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: none;
        background: #ef4444;
        color: #fff;
        font-size: 14px;
        line-height: 18px;
        cursor: pointer;
        padding: 0;
    }

    .child-remove-btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .add-child-box {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #d1d5db;
    }

    .empty-family {
        padding: 24px;
        border-radius: 16px;
        background: #fafafa;
        color: #6b7280;
        font-style: italic;
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

    {{-- Заголовок --}}
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
        @else
            <button class="btn btn-sm btn-outline-primary"
                    disabled
                    title="Добавлять партнёров могут только владелец или редактор">
                ➕ Добавить партнёра
            </button>
        @endcan
    </div>

    <div id="relationship-form-container" class="d-none mb-3">
        @include('people.partials.relationship-form')
    </div>

    @if($person->couples->isEmpty())
        <div class="empty-family">
            Пока здесь нет семейных связей.
            Добавление доступно владельцу или редактору семьи.
        </div>
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

                    $count = $children->count();

                    /*
|--------------------------------------------------------------------------
| Определение даты окончания брака
|--------------------------------------------------------------------------
*/

$endDate = $couple->ended_at
    ?? $couple->end_date
    ?? $couple->divorced_at
    ?? null;

// 🔹 ВАЖНО: всегда объявляем переменную
$endedByDeath = false;

if (!$endDate) {

    // смерть супруга
    if ($spouse?->death_date) {
        $endDate = $spouse->death_date;
        $endedByDeath = true;
    }

    // смерть текущего человека
    elseif ($person->death_date) {
        $endDate = $person->death_date;
        $endedByDeath = true;
    }
}

$isEnded = !empty($endDate);

/*
|--------------------------------------------------------------------------
| 🕊 Если дата окончания не указана,
| но один из супругов умер — считаем брак завершённым
|--------------------------------------------------------------------------
*/

$deathDate = null;

if (!$endDate) {

    if ($spouse?->death_date) {
        $deathDate = $spouse->death_date;
    }

    if ($person->death_date) {
        $deathDate = $person->death_date;
    }

    if ($deathDate) {
        $endDate = $deathDate;
    }
}

$isEnded = !empty($endDate);
                @endphp

                <div class="marriage-card {{ $relation['class'] }} {{ $isEnded ? 'marriage-ended' : 'marriage-active' }}">

                    <div class="marriage-header">
                        <div class="marriage-title">
                            {{ $relation['icon'] }} {{ $relation['label'] }}

                            <span class="badge-status {{ $isEnded ? 'badge-ended' : 'badge-active' }}">
                                {{ $isEnded ? 'Расторгнут' : 'Действующий' }}
                            </span>
                        </div>

                        @if($couple->started_at || $endDate)

                            <div class="marriage-period">
                                {{ $couple->started_at ? \Carbon\Carbon::parse($couple->started_at)->year : '?' }}
                                —
                                {{ $endDate ? \Carbon\Carbon::parse($endDate)->year : 'н.в.' }}
                            </div>

                            @if($endedByDeath)
                                <div class="small text-muted mt-1" style="font-style: italic;">
                                    🕯 Брак длился до ухода супруга в {{ \Carbon\Carbon::parse($endDate)->year }} году
                                </div>
                            @endif

                        @endif

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

                    {{-- Весь остальной твой код детей и кнопок остаётся без изменений ниже --}}

                </div>

            @endforeach
        </div>
    @endif
    <script>
        function toggleRelationshipForm() {
            const el = document.getElementById('relationship-form-container');
            if (!el) return;

            el.classList.toggle('d-none');

            if (!el.classList.contains('d-none')) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function toggleAddChild(id) {
            const el = document.getElementById('add-child-box-' + id);
            if (!el) return;

            el.classList.toggle('d-none');

            if (!el.classList.contains('d-none')) {
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    </script>

</div>
