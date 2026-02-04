<style>
    .marriages {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .marriage-card {
        background: #fff;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(0,0,0,.05);
        border-left: 6px solid transparent;
        animation: fadeIn .25s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: none; }
    }

    .relation-marriage { border-left-color: #f59e0b; }
    .relation-civil    { border-left-color: #6366f1; }
    .relation-parents  { border-left-color: #06b6d4; }

    .marriage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        gap: 8px;
    }

    .marriage-title {
        font-weight: 600;
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .marriage-dates {
        font-size: 13px;
        color: #6b7280;
    }

    /* ===== SPOUSE ===== */
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

    /* ===== CHILDREN ===== */
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

    /* ===== ADD CHILD ===== */
    .add-child-box {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #d1d5db;
    }

    /* ===== Отвязка детей ===== */
    .child-card {
        position: relative;
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

    .child-remove-btn:hover {
        background: #dc2626;
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

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Связи и дети</h2>
    <button class="btn btn-sm btn-outline-primary"
            onclick="document.getElementById('relationship-form-container').classList.toggle('d-none')">
        ➕ Создать связь
    </button>
</div>

<div id="relationship-form-container" class="d-none">
    @include('people.partials.relationship-form')
</div>

<div class="marriages">

    @forelse($person->couples as $couple)
        @php
            $type = $couple->relation_type ?? 'marriage';
            $relation = $relationMap[$type];

            $spouse = $couple->person_1_id === $person->id
                ? $couple->person2
                : $couple->person1;

            $start = $couple->married_at ? Carbon::parse($couple->married_at) : null;
            $end = $couple->divorced_at
    ? Carbon::parse($couple->divorced_at)
    : (
        $spouse?->death_date
            ? Carbon::parse($spouse->death_date)
            : (
                $person->death_date
                    ? Carbon::parse($person->death_date)
                    : null
            )
    );
        @endphp

        <div class="marriage-card {{ $relation['class'] }}">

            <div class="marriage-header">
                <div class="marriage-title">
                    {{ $relation['icon'] }} {{ $relation['label'] }}
                </div>
                <div class="marriage-dates">
                    @if($type === 'parents')
                        Совместные родители
                    @else
                        {{ $start ? 'с '.$start->year : '' }}
                        {{ $end ? 'по '.$end->year : 'по настоящее время' }}

                        @if($couple->divorced_at)
                            · развод
                        @elseif($spouse?->death_date || $person->death_date)
                            · до смерти
                        @endif
                    @endif
                </div>
            </div>

            {{-- СПУТНИК --}}
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
                        <strong>{{ $spouse->last_name }} {{ $spouse->first_name }}</strong><br>
                        <small class="text-muted">
                            {{ $spouse->birth_date ? Carbon::parse($spouse->birth_date)->year : '?' }}
                            —
                            {{ $spouse->death_date ? Carbon::parse($spouse->death_date)->year : 'н.в.' }}
                        </small>
                    </div>
                </div>
            @endif

            {{-- 👶 ДЕТИ --}}
            @php
                $children = $couple->children
                    ->sortBy(fn($c) => $c->birth_date ?? '9999-12-31')
                    ->values();

                $count = $children->count();
            @endphp

            <div class="children">
                @foreach($children as $i => $child)
                    @php
                        // порядок
                        if ($count < 2) {
                            $order = null;
                        } elseif ($i === 0) {
                            $order = 'Старший';
                        } elseif ($i === $count - 1) {
                            $order = 'Младший';
                        } else {
                            $order = 'Средний';
                        }

                        $role = $child->gender === 'male' ? 'Сын' : 'Дочь';
                    @endphp

                    <div class="child-card"
                         onclick="window.location.href='{{ route('people.show', $child) }}'">

                        {{-- ❌ УБРАТЬ ИЗ БРАКА --}}
                        <form method="POST"
                              action="{{ route('couples.children.detach', [$couple, $child]) }}"
                              onsubmit="return confirm('Убрать ребёнка из этого брака?')"
                              class="child-remove"
                              onclick="event.stopPropagation()">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="child-remove-btn"
                                    title="Убрать из брака">
                                ✕
                            </button>
                        </form>
                        <img class="child-photo"
                             src="{{ $child->photo
                    ? asset('storage/'.$child->photo)
                    : route('avatar', [
                        'name' => mb_substr($child->first_name,0,1).mb_substr($child->last_name ?? '',0,1),
                        'gender' => $child->gender
                    ]) }}">

                        <div class="child-name">
                            {{ $child->first_name }}
                        </div>

                        <div class="child-role">
                            {{ $role }}
                            @if($order)
                                · {{ $order }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ➕ ДОБАВИТЬ РЕБЁНКА --}}
            <div class="add-child-box mt-2">

                {{-- ➕ НОВЫЙ --}}
                <form method="POST"
                      action="{{ route('couples.children.store', $couple) }}"
                      class="mb-2">
                    @csrf

                    <div class="d-flex gap-2 align-items-center">
                        <input name="first_name"
                               class="form-control form-control-sm"
                               placeholder="Имя"
                               required>

                        <input name="last_name"
                               class="form-control form-control-sm"
                               placeholder="Фамилия">

                        <button class="btn btn-sm btn-outline-primary"
                                title="Добавить ребёнка">
                            ➕
                        </button>
                    </div>
                </form>

                {{-- 🔗 СУЩЕСТВУЮЩИЙ --}}
                @if(isset($existingChildrenCandidates) && $existingChildrenCandidates->count())
                    <form method="POST"
                          action="{{ route('couples.children.attach', $couple) }}">
                        @csrf

                        <div class="d-flex gap-2 align-items-center">
                            <select name="child_id"
                                    class="form-select form-select-sm"
                                    required>
                                <option value="">Выбрать существующего ребёнка</option>
                                @foreach($existingChildrenCandidates as $candidate)
                                    <option value="{{ $candidate->id }}">
                                        {{ $candidate->last_name }} {{ $candidate->first_name }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="btn btn-sm btn-outline-secondary"
                                    title="Привязать">
                                🔗
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>

    @empty
        <div class="text-muted fst-italic">
            У человека пока нет зарегистрированных связей
        </div>
    @endforelse

</div>
