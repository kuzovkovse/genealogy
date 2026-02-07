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
    }

    .relation-marriage { border-left-color: #f59e0b; }
    .relation-civil    { border-left-color: #6366f1; }
    .relation-parents  { border-left-color: #06b6d4; }

    .marriage-header {
        margin-bottom: 12px;
    }

    .marriage-title {
        font-weight: 600;
        display: flex;
        gap: 6px;
        align-items: center;
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
        position: relative;
    }

    .child-card:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
    }

    .child-avatar {
        position: relative;
        display: inline-block;
    }

    .child-photo {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 4px;
        border: 2px solid #e5e7eb;
    }

    .child-camera {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #2563eb;
        color: #fff;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        cursor: pointer;
        z-index: 5;
    }

    .child-camera:hover {
        background: #1d4ed8;
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

    .child-remove-btn:hover {
        background: #dc2626;
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
        <div class="empty-family">
            Пока здесь нет семейных связей.
            Начните с добавления партнёра — дальше появятся дети и события жизни.
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
                @endphp

                <div class="marriage-card {{ $relation['class'] }}">
                    <div class="marriage-header">
                        <div class="marriage-title">
                            {{ $relation['icon'] }} {{ $relation['label'] }}
                        </div>
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
                                <strong>{{ $spouse->last_name }} {{ $spouse->first_name }}</strong><br>
                                <small class="text-muted">
                                    {{ $spouse->birth_date ? Carbon::parse($spouse->birth_date)->year : '?' }}
                                    —
                                    {{ $spouse->death_date ? Carbon::parse($spouse->death_date)->year : 'н.в.' }}
                                </small>
                            </div>
                        </div>
                    @endif

                    @if($count)
                        <div class="children">
                            @foreach($children as $i => $child)
                                @php
                                    $order =
                                        $count < 2 ? null :
                                        ($i === 0 ? 'Старший' : ($i === $count - 1 ? 'Младший' : 'Средний'));

                                    $role = $child->gender === 'male' ? 'Сын' : 'Дочь';
                                @endphp

                                <div class="child-card"
                                     onclick="window.location.href='{{ route('people.show', $child) }}'">

                                    @can('manageChildren', $couple)
                                        <form method="POST"
                                              action="{{ route('couples.children.detach', [$couple, $child]) }}"
                                              onsubmit="return confirm('Убрать ребёнка из этой семьи?')"
                                              class="child-remove"
                                              onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button class="child-remove-btn">✕</button>
                                        </form>
                                    @endcan

                                    <div class="child-avatar">
                                        <img class="child-photo"
                                             src="{{ $child->photo
                                                ? asset('storage/'.$child->photo)
                                                : route('avatar', [
                                                    'name' => mb_substr($child->first_name,0,1).mb_substr($child->last_name ?? '',0,1),
                                                    'gender' => $child->gender
                                                ]) }}">

                                        @if(!$child->photo)
                                            <a href="{{ route('people.edit', $child) }}"
                                               class="child-camera"
                                               title="Добавить фотографию"
                                               onclick="event.stopPropagation()">📷</a>
                                        @endif
                                    </div>

                                    <div class="child-name">{{ $child->first_name }}</div>
                                    <div class="child-role">
                                        {{ $role }} @if($order) · {{ $order }} @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        @if($count === 0)
                            <div class="text-muted small mt-2">
                                У этой семьи пока не указаны дети
                                @can('manageChildren', $couple)
                                    — добавьте первого
                                @endcan
                            </div>
                        @endif

                    @can('manageChildren', $couple)
                    <button class="btn btn-sm btn-link text-muted p-0 mt-2"
                            onclick="toggleAddChild({{ $couple->id }})">
                        ➕ Добавить ребёнка в эту семью
                    </button>
                    @endcan

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
                                                {{ $candidate->last_name }} {{ $candidate->first_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-secondary">🔗</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function toggleAddChild(id) {
        const el = document.getElementById('add-child-box-' + id);
        if (!el) return;
        el.classList.toggle('d-none');
        if (!el.classList.contains('d-none')) {
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
</script>
