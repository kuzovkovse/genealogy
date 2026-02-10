@extends('layouts.app')

@section('title', 'Люди')

@section('content')

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Люди</h1>

        @can('create', \App\Models\Person::class)
            <a href="{{ route('people.create') }}" class="btn btn-primary">
                ➕ Добавить человека
            </a>
        @endcan
    </div>

    {{-- ================= SEARCH & FILTERS ================= --}}
    <div class="mb-4">

        <input
            type="text"
            id="peopleSearch"
            class="form-control mb-2"
            placeholder="🔍 Поиск по имени, фамилии или отчеству…">

        <div class="d-flex flex-wrap gap-3 align-items-center small text-muted">

            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" class="filter-gender" value="male">
                👨 Мужчины
            </label>

            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" class="filter-gender" value="female">
                👩 Женщины
            </label>

            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" id="filter-war">
                🎖 Участники ВОВ
            </label>

            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" id="filter-alive">
                ❤️ Живые
            </label>

            <label class="d-flex align-items-center gap-1">
                <input type="checkbox" id="filter-dead">
                🕯 Умершие
            </label>

        </div>
    </div>

    <style>
        .generation-block { margin-bottom: 48px; }

        .generation-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }

        .people-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .person-card {
            position: relative;
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .person-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            transform: translateY(-3px);
        }

        .person-card.alive { border-color: #86efac; }
        .person-card.dead  { border-color: #d1d5db; filter: grayscale(35%) contrast(.95); }

        .person-photo {
            width: 100%;
            height: 220px;
            background: #f3f4f6;
        }

        .person-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .person-name {
            padding: 12px 12px 4px;
            font-weight: 600;
            text-align: center;
            line-height: 1.3;
        }

        .person-life {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            padding-bottom: 10px;
        }

        .person-link {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .tree-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 3;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,.95);
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
        }

        .badges {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 3;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .badge {
            font-size: 11px;
            padding: 3px 7px;
            border-radius: 999px;
            background: rgba(255,255,255,.9);
            box-shadow: 0 2px 6px rgba(0,0,0,.12);
            white-space: nowrap;
        }

        .badge-war   { color: #92400e; }
        .badge-alive { color: #065f46; }
        .badge-dead  { color: #7f1d1d; }
    </style>

    {{-- ================= GENERATIONS ================= --}}
    @forelse($generations as $level => $people)
        <div class="generation-block">

            <div class="generation-title">
                {{ roman($level) }} поколение
                <span class="text-muted" style="font-size:13px;">
            ({{ $people->count() }})
        </span>
            </div>

            <div class="people-grid">
                @foreach($people as $person)

                    @php
                        $fullName = trim(
                            ($person->last_name ?? '').' '.
                            ($person->first_name ?? '').' '.
                            ($person->patronymic ?? '')
                        );

                        $birthYear = $person->birth_date ? \Carbon\Carbon::parse($person->birth_date)->year : null;
                        $deathYear = $person->death_date ? \Carbon\Carbon::parse($person->death_date)->year : null;

                        if ($birthYear) {
                            $lifeLine = $deathYear
                                ? "$birthYear — $deathYear"
                                : "род. $birthYear";
                        } else {
                            $lifeLine = null;
                        }
                    @endphp

                    <div class="person-card {{ $person->death_date ? 'dead' : 'alive' }}"
                         data-name="{{ mb_strtolower($fullName) }}"
                         data-gender="{{ $person->gender }}"
                         data-war="{{ $person->is_war_participant ? '1' : '0' }}"
                         data-life="{{ $person->death_date ? 'dead' : 'alive' }}">

                        <a href="{{ route('people.show', $person) }}" class="person-link"></a>

                        <button class="tree-btn"
                                onclick="event.stopPropagation(); window.location='{{ route('tree.view', $person) }}'">
                            🌳
                        </button>

                        <div class="badges">
                            @if($person->is_war_participant)
                                <div class="badge badge-war">🎖 ВОВ</div>
                            @endif

                            @if($person->death_date)
                                <div class="badge badge-dead">🕯 Умер</div>
                            @else
                                <div class="badge badge-alive">❤️ Жив</div>
                            @endif
                        </div>

                        <div class="person-photo">
                            <img src="{{ $person->photo ? asset('storage/'.$person->photo) : asset('storage/people/placepeople.png') }}">
                        </div>

                        <div class="person-name">{{ $fullName }}</div>

                        @if($lifeLine)
                            <div class="person-life">{{ $lifeLine }}</div>
                        @endif
                    </div>

                @endforeach
            </div>
        </div>
    @empty
        <p class="text-muted">Пока нет ни одного человека</p>
    @endforelse

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const STORAGE_KEY = 'people_filters_v1';

            const search = document.getElementById('peopleSearch');
            const genders = document.querySelectorAll('.filter-gender');
            const war = document.getElementById('filter-war');
            const alive = document.getElementById('filter-alive');
            const dead = document.getElementById('filter-dead');

            /* ================= SAVE ================= */
            function saveState() {
                const state = {
                    search: search.value,
                    genders: [...genders].filter(cb => cb.checked).map(cb => cb.value),
                    war: war.checked,
                    alive: alive.checked,
                    dead: dead.checked,
                };

                localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            }

            /* ================= RESTORE ================= */
            function restoreState() {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) return;

                try {
                    const state = JSON.parse(raw);

                    if (typeof state.search === 'string') {
                        search.value = state.search;
                    }

                    if (Array.isArray(state.genders)) {
                        genders.forEach(cb => {
                            cb.checked = state.genders.includes(cb.value);
                        });
                    }

                    war.checked = !!state.war;
                    alive.checked = !!state.alive;
                    dead.checked = !!state.dead;

                } catch (e) {
                    console.warn('Failed to restore filters', e);
                }
            }

            /* ================= APPLY ================= */
            function applyFilters() {
                const q = search.value.toLowerCase().trim();

                const selectedGenders = [...genders]
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                document.querySelectorAll('.person-card').forEach(card => {
                    let visible = true;

                    if (q && !card.dataset.name.includes(q)) visible = false;
                    if (selectedGenders.length && !selectedGenders.includes(card.dataset.gender)) visible = false;
                    if (war.checked && card.dataset.war !== '1') visible = false;
                    if (alive.checked && card.dataset.life !== 'alive') visible = false;
                    if (dead.checked && card.dataset.life !== 'dead') visible = false;

                    card.style.display = visible ? '' : 'none';
                });
            }

            /* ================= EVENTS ================= */
            function onChange() {
                saveState();
                applyFilters();
            }

            [search, war, alive, dead, ...genders]
                .forEach(el => el.addEventListener('input', onChange));

            /* ================= INIT ================= */
            restoreState();
            applyFilters();
        });
    </script>

@endsection
