@extends('layouts.app')

@section('title', 'Люди')

@section('content')

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Люди</h1>

        @can('create', \App\Models\Person::class)
            <a href="{{ route('people.create') }}" class="btn btn-primary">
                ➕ Добавить человека
            </a>
        @endcan
    </div>

    {{-- ================= MODE SWITCHER ================= --}}
    <div class="mb-4 d-flex gap-2">

        <a href="{{ route('people.index', ['mode' => 'structure']) }}"
           class="btn btn-sm {{ $mode === 'structure' ? 'btn-primary' : 'btn-outline-primary' }}">
            👨‍👩‍👧 Семейная структура
        </a>

        <a href="{{ route('people.index', ['mode' => 'blood']) }}"
           class="btn btn-sm {{ $mode === 'blood' ? 'btn-primary' : 'btn-outline-primary' }}">
            🧬 Генеалогия
        </a>

        <a href="{{ route('people.index', ['mode' => 'list']) }}"
           class="btn btn-sm {{ $mode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
            📋 Общий список
        </a>

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

    {{-- ================= STYLES ================= --}}
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
            padding: 4px 8px;
            border-radius: 999px;
            background: #ffffff;
            color: #111827; /* 👈 тёмный текст */
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,0,0,.15);
            white-space: nowrap;
        }

        .badge-war {
            background: #fff7ed;
            color: #9a3412;
        }

        .badge-alive {
            background: #ecfdf5;
            color: #065f46;
        }

        .badge-dead {
            background: #f3f4f6;
            color: #7f1d1d;
        }
        .badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 999px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,0,0,.15);
            white-space: nowrap;
        }

        .badge-war {
            background: #fff7ed;
            color: #9a3412;
        }

        .badge-alive {
            background: #ecfdf5;
            color: #065f46;
        }

        .badge-dead {
            background: #f3f4f6;
            color: #7f1d1d;
        }

        .badge-root {
            background: #fef3c7;
            color: #92400e;
        }

        /* ===== РОДОНАЧАЛЬНИК ===== */

        .root-person {
            border: 2px solid #f59e0b !important;
            box-shadow: 0 0 0 3px rgba(245,158,11,.25);
        }

        /* =========================
   🧬 ВЕРТИКАЛЬНАЯ ЛИНИЯ РОДА
========================= */

        .generations-wrapper {
            position: relative;
            padding-left: 40px;
            z-index: 1;
        }

        .generations-wrapper::before {
            content: '';
            position: absolute;
            left: 18px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(
                to bottom,
                rgba(139,94,60,0.2),
                rgba(139,94,60,0.4),
                rgba(139,94,60,0.2)
            );
        }

        /* =========================
           📚 ПОКОЛЕНИЯ
        ========================= */

        .generation-block {
            position: relative;
            margin-bottom: 60px;
            padding-left: 20px;
        }

        .generation-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 600;
            padding-bottom: 10px;
            margin-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
        }

        /* эффект глубины */
        .generation-block[data-level="1"] { margin-top: 0; }
        .generation-block[data-level="2"] { margin-left: 10px; }
        .generation-block[data-level="3"] { margin-left: 20px; }
        .generation-block[data-level="4"] { margin-left: 30px; }
        .generation-block[data-level="5"] { margin-left: 40px; }

        /* мягкий фон для старших поколений */
        .generation-block[data-level="1"] {
            background: linear-gradient(to right, #faf7f2, transparent);
            padding: 20px;
            border-radius: 12px;
        }

        /* I поколение крупнее */
        .generation-block[data-level="1"] .person-card {
            transform: scale(1.05);
        }

        /* скрываемый контент */
        .generation-content.collapsed {
            display: none;
        }

        /* якорная навигация */
        .generation-nav {
            position: sticky;
            top: 70px;
            margin-bottom: 30px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;

            background: #f9fafb;
            padding: 10px 12px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,.08);

            z-index: 1000;
        }

    </style>

    {{-- ================= GENERATIONS MODE ================= --}}
    @if($mode !== 'list')

        @php $totalGenerations = count($generations); @endphp

        <div class="mb-3 text-muted">
            🧬 Поколений: {{ $totalGenerations }}
        </div>

        <div class="generation-nav">
            @foreach($generations as $level => $people)
                <button class="btn btn-sm btn-outline-secondary"
                        onclick="scrollToGeneration({{ $level }})">
                    {{ roman($level) }}
                </button>
            @endforeach
        </div>

        <div class="generations-wrapper">

        @forelse($generations as $level => $people)
                <div class="generation-block"
                     id="generation-{{ $level }}"
                     data-level="{{ $level }}">

                    <div class="generation-title"
                         onclick="toggleGeneration({{ $level }})">
                    {{ roman($level) }} поколение
                    <span class="text-muted" style="font-size:13px;">
                        ({{ $people->count() }})
                    </span>
                </div>
                    <div class="generation-content"
                         id="generation-content-{{ $level }}">
                <div class="people-grid">
                    @foreach($people as $person)
                        @include('people.partials.person-card')
                    @endforeach
                </div>
                    </div>
            </div>
        @empty
            <p class="text-muted">Пока нет ни одного человека</p>
        @endforelse
        </div> {{-- generations-wrapper --}}
    @endif


    {{-- ================= LIST MODE ================= --}}
    @if($mode === 'list')

        <div class="people-grid">
            @foreach($peopleList as $person)
                @include('people.partials.person-card')
            @endforeach
        </div>

    @endif


    {{-- ================= FILTER SCRIPT ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const STORAGE_KEY = 'people_filters_v1';

            const search = document.getElementById('peopleSearch');
            const genders = document.querySelectorAll('.filter-gender');
            const war = document.getElementById('filter-war');
            const alive = document.getElementById('filter-alive');
            const dead = document.getElementById('filter-dead');

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

            function restoreState() {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) return;

                const state = JSON.parse(raw);

                search.value = state.search || '';
                genders.forEach(cb => cb.checked = state.genders?.includes(cb.value));
                war.checked = !!state.war;
                alive.checked = !!state.alive;
                dead.checked = !!state.dead;
            }

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

            function onChange() {
                saveState();
                applyFilters();
            }

            [search, war, alive, dead, ...genders]
                .forEach(el => el.addEventListener('input', onChange));

            restoreState();
            applyFilters();
        });
        function toggleGeneration(level) {
            const content = document.getElementById('generation-content-' + level);
            if (!content) return;
            content.classList.toggle('collapsed');
        }

        function scrollToGeneration(level) {
            const el = document.getElementById('generation-' + level);
            if (!el) return;

            el.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

    </script>

@endsection
