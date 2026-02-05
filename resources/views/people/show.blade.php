@extends('layouts.app')
@section('title', $person->last_name . ' ' . $person->first_name)

@section('content')

    <style>
        /* ===== HERO ===== */
        .person-hero {
            display: flex;
            gap: 24px;
            padding: 24px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
            margin-bottom: 32px;
            align-items: center;
        }

        .person-hero.dead {
            opacity: .85;
        }

        .person-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e5e7eb;
        }

        .person-main {
            flex: 1;
        }

        .person-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .person-life {
            font-size: 15px;
            color: #555;
            margin-bottom: 10px;
        }

        .person-life .candle {
            margin-left: 6px;
        }

        .badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .gp-badge {
            display: inline-block;
            margin-bottom: 4px;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 999px;
            background: #f5efe6;
            color: #6b4f2a;
            font-weight: 600;
        }

        .badge {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #374151;
        }

         .biography-card {
             background: #fff;
             border-radius: 16px;
             padding: 24px;
             box-shadow: 0 8px 24px rgba(0,0,0,.05);
             margin-bottom: 32px;
         }

        .timeline-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,.05);
            margin-bottom: 32px;
        }

        .biography-text {
            white-space: pre-line;
            line-height: 1.6;
            color: #374151;
        }

        .biography-empty {
            color: #9ca3af;
            font-style: italic;
        }

    .badge.male {
            background: #e0ecff;
            color: #1e3a8a;
        }

        .badge.female {
            background: #ffe4f0;
            color: #9d174d;
        }

        .badge.dead {
            background: #111827;
            color: #fff;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* ===== PARENTS ===== */
        .parents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 40px;
        }

        .parent-card {
            display: flex;
            gap: 16px;
            padding: 16px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0,0,0,.05);
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        .parent-card.dead {
            border: 2px dashed #9ca3af;
            opacity: .85;
        }

        .parent-photo {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e5e7eb;
        }

        .parent-name {
            font-weight: 600;
        }

        .parent-life {
            font-size: 13px;
            color: #555;
        }

        /* =========================
   🕯 MEMORIAL MODE
   ========================= */

        .memorial {
            --memorial-gray: #9ca3af;
            --memorial-border: #d1d5db;
        }

        /* Фото → Ч/Б */
        .memorial .person-photo,
        .memorial .avatar,
        .memorial img {
            filter: grayscale(100%);
        }

        /* Заголовки */
        .memorial h1,
        .memorial h2,
        .memorial h3,
        .memorial .person-name {
            color: #111827;
        }

        /* Карточки */
        .memorial .card,
        .memorial .person-hero {
            border: 1px dashed var(--memorial-border);
            background: #fafafa;
        }

        /* Текст вторичный */
        .memorial .text-muted,
        .memorial .life-years {
            color: var(--memorial-gray);
        }

        /* Свеча */
        .memorial-candle {
            margin-left: 6px;
            font-size: 1.1em;
        }

        /* sticky-кнопки */
                .hero-actions {
            display: flex;
            gap: 10px;
            align-items: center;

            position: sticky;
            top: 20px;              /* отступ от верха */
            align-self: flex-start; /* важно для flex */
            z-index: 10;
        }

        /* чуть компактнее кнопки при залипании */
        .hero-actions .btn {
            white-space: nowrap;
        }

        /* ===== GRANDPARENTS WOOD BLOCK ===== */
        .grandparents-block {
            position: relative;
            margin-bottom: 40px;
            padding: 24px 24px 12px;

            background:
                linear-gradient(180deg, #faf7f2, #f6f1e8),
                radial-gradient(circle at top left, rgba(139,94,60,0.08), transparent 60%),
                radial-gradient(circle at bottom right, rgba(160,120,80,0.06), transparent 55%);

            border-radius: 20px;
            box-shadow:
                inset 0 0 0 1px rgba(139,94,60,0.12),
                0 10px 30px rgba(0,0,0,.04);
        }
        /* ===== СВОДНЫЕ ===== */
        .siblings-block {
            padding: 24px;
            border-radius: 20px;
            background:
                linear-gradient(180deg, #f7fafc, #f1f5f9);
            box-shadow:
                inset 0 0 0 1px rgba(0,0,0,.04),
                0 8px 24px rgba(0,0,0,.04);
        }

        .half-siblings-block {
            padding: 24px;
            border-radius: 20px;
            background:
                linear-gradient(180deg, #fafafa, #f4f4f5);
            box-shadow:
                inset 0 0 0 1px rgba(0,0,0,.05),
                0 6px 18px rgba(0,0,0,.04);
        }

        /* =========================
    🕯 МЕСТО ПАМЯТИ
    ========================= */

        .memorial-place-block {
            margin-top: 48px;
        }

        .memorial-card {
            background:
                linear-gradient(180deg, #fafafa, #f6f6f6),
                radial-gradient(circle at top left, rgba(120,120,120,0.05), transparent 60%);
            border-radius: 18px;
            padding: 24px;
            box-shadow:
                inset 0 0 0 1px rgba(0,0,0,0.06),
                0 10px 24px rgba(0,0,0,0.04);
        }

        /* сетка данных */
        .memorial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .memorial-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .memorial-value {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }

        /* описание как найти */
        .memorial-description {
            font-size: 14px;
            line-height: 1.6;
            color: #374151;
            background: #ffffff;
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
        }

        /* кнопка карты */
        .memorial-card .btn {
            border-radius: 999px;
        }

        /* подпись */
        .memorial-note {
            font-size: 12px;
            color: #6b7280;
            font-style: italic;
        }

        /* empty state */
        .memorial-empty {
            padding: 32px 16px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
            background:
                repeating-linear-gradient(
                    45deg,
                    #f9fafb,
                    #f9fafb 10px,
                    #f3f4f6 10px,
                    #f3f4f6 20px
                );
            border-radius: 14px;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
        }

        /* memorial mode — чуть строже */
        .memorial .memorial-card {
            background:
                linear-gradient(180deg, #f7f7f7, #f1f1f1);
        }



    </style>

    @php
        use Carbon\Carbon;

        $birth = $person->birth_date ? Carbon::parse($person->birth_date) : null;
        $death = $person->death_date ? Carbon::parse($person->death_date) : null;

        $age = $birth
            ? ($death ? $birth->diffInYears($death) : $birth->age)
            : null;
    @endphp
    @php
        $isMemorial = (bool) $person->death_date;
    @endphp
    <div class="{{ $isMemorial ? 'memorial' : '' }}">
    {{-- ================= HERO ================= --}}
        <div class="person-hero {{ $person->death_date ? 'dead' : '' }}">

            {{-- ЛЕВАЯ ЧАСТЬ --}}
            <img
                src="{{ $person->photo ? asset('storage/'.$person->photo) : route('avatar', ['name' => mb_substr($person->first_name,0,1).mb_substr($person->last_name,0,1), 'gender' => $person->gender]) }}"
                class="person-photo"
            >

            <div class="person-main">
                <div class="person-name">
                    {{ $person->last_name }} {{ $person->first_name }}

                    @if(
                        $person->gender === 'female'
                        && $person->birth_last_name
                        && $person->birth_last_name !== $person->last_name
                    )
                        <span class="text-muted" style="font-size:14px; font-weight:400;">
            (урожд. {{ $person->birth_last_name }})
        </span>
                    @endif
                </div>

                <div class="person-life">
                    {{ $birth?->year ?? '?' }} — {{ $death?->year ?? 'н.в.' }}
                    @if($age) · {{ (int) floor($age) }} лет @endif
                    @if($death) <span class="candle">🕯</span> @endif
                </div>

                <div class="badges">
            <span class="badge {{ $person->gender }}">
                {{ $person->gender === 'male' ? 'Мужчина' : 'Женщина' }}
            </span>

                    @if($person->death_date)
                        <span class="badge dead">Умер</span>
                    @else
                        <span class="badge">Жив</span>
                    @endif
                </div>
            </div>

            {{-- ПРАВАЯ ЧАСТЬ --}}
            <div class="hero-actions">
                <a href="{{ route('people.edit', $person) }}" class="btn btn-outline-primary">
                    ✏️ Редактировать
                </a>
                <div class="form-check form-switch ms-2">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="extendedKinshipToggle"
                        {{ $kinship->extended ? 'checked' : '' }}
                    >
                    <label class="form-check-label small text-muted" for="extendedKinshipToggle">
                        Расширенное родство
                    </label>
                </div>
                @if($person->public_uuid)
                    <a href="{{ route('people.public', ['uuid' => $person->public_uuid]) }}"
                       target="_blank"
                       class="btn btn-outline-secondary ms-2">
                        🔗 Публичная ссылка
                    </a>
                @endif
            </div>

        </div>

        {{-- ================= РОДИТЕЛИ ================= --}}
        <h3 class="mb-3">Родители</h3>

        <div class="parents-grid">

            @foreach([ 'Отец' => $father ?? null, 'Мать' => $mother ?? null ] as $label => $parent)
                @if($parent)
                    @php
                        $pb = $parent->birth_date ? Carbon::parse($parent->birth_date) : null;
                        $pd = $parent->death_date ? Carbon::parse($parent->death_date) : null;
                    @endphp

                    <a href="{{ route('people.show', $parent) }}"
                       class="parent-card {{ $parent->death_date ? 'dead' : '' }}">

                        <img class="parent-photo"
                             src="{{ $parent->photo
                        ? asset('storage/'.$parent->photo)
                        : route('avatar', [
                            'name' => mb_substr($parent->first_name,0,1)
                                    .mb_substr($parent->last_name ?? '',0,1),
                            'gender' => $parent->gender
                        ])
                     }}">

                        {{-- ТЕКСТОВЫЙ БЛОК --}}
                        <div>
                            <div class="parent-name">
                                {{ $label }} · {{ $parent->last_name }} {{ $parent->first_name }}

                                @if(
                                    $parent->gender === 'female'
                                    && $parent->birth_last_name
                                    && $parent->birth_last_name !== $parent->last_name
                                )
                                    <span class="text-muted"
                                          style="font-size:12px; font-weight:400; margin-left:4px;">
                                (урожд. {{ $parent->birth_last_name }})
                            </span>
                                @endif
                            </div>

                            <div class="parent-life">
                                {{ $pb?->year ?? '?' }} — {{ $pd?->year ?? 'н.в.' }}
                                @if($pd) 🕯 @endif
                            </div>
                        </div>

                    </a>
                @else
                    <div class="parent-card">
                        <div>
                            <div class="parent-name">{{ $label }}</div>
                            <div class="parent-life text-muted">Не указан</div>
                        </div>
                    </div>
                @endif
            @endforeach

        </div>

        {{-- ================== ДЕДЫ И БАБУШКИ ================== --}}
        @include('people.partials.grandparents')

        {{-- ================== ПРАДЕДЫ И ПРАБАБУШКИ ================== --}}
        @if($kinship->extended)
            @include('people.partials.great-grandparents', ['kinship' => $kinship])
        @endif

        {{-- ================== БРАТЬЯ И СЁСТРЫ ================== --}}
        @include('people.partials.siblings', [
            'siblings' => $kinship->siblings
        ])
    {{-- ================= БРАКИ ================= --}}
    @include('people.partials.marriages')
    {{-- ================== МЕСТО ПАМЯТИ ================== --}}
    @include('people.partials.memorial-place')
    {{-- ================= ХРОНОЛОГИЯ ================= --}}
    @include('people.partials.timeline')
    {{-- ================== БИОГРАФИЯ ================== --}}
    <div class="biography-card">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">📖 История жизни</h3>
            <button class="btn btn-sm btn-outline-primary"
                    onclick="toggleBiographyEdit()">
                ✏️ Редактировать
            </button>
        </div>

        {{-- VIEW --}}
        <div id="biography-view">
            @if($person->biography)
                <div class="biography-text">
                    {!! nl2br(e($person->biography)) !!}
                </div>
            @else
                <div class="biography-empty">
                    История жизни пока не заполнена
                </div>
            @endif
        </div>

        {{-- EDIT --}}
        <div id="biography-edit" style="display:none;">
            <form method="POST"
                  action="{{ route('people.biography.update', $person) }}">
                @csrf
                @method('PATCH')

                <textarea name="biography"
                          class="form-control mb-3"
                          rows="8"
                          placeholder="Опишите историю жизни человека...">{{ old('biography', $person->biography) }}</textarea>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">💾 Сохранить</button>
                    <button type="button"
                            class="btn btn-outline-secondary"
                            onclick="toggleBiographyEdit()">
                        Отмена
                    </button>
                </div>
            </form>
        </div>

    </div>
    {{-- ================== ФОТОГАЛЛЕРЕЯ ================== --}}
    @include('people.partials.gallery')
    {{-- ================== ДОБАВЛЕНИЕ ФОТО ================== --}}
    @include('people.partials.photo-form')
    {{-- ================== ДОБАВЛЕНИЕ ДОКУМЕНТОВ ================== --}}
    @include('people.partials.documents')

    <a href="{{ route('people.index') }}" class="btn btn-link">← Назад</a>
    <script>
        function toggleBiographyEdit() {
            const view = document.getElementById('biography-view');
            const edit = document.getElementById('biography-edit');

            if (edit.style.display === 'none') {
                view.style.display = 'none';
                edit.style.display = 'block';
            } else {
                edit.style.display = 'none';
                view.style.display = 'block';
            }
        }
    </script>
        <script>
            function toggleMemorialEdit() {
                const view = document.getElementById('memorial-view');
                const edit = document.getElementById('memorial-edit');

                if (!view || !edit) {
                    console.warn('Memorial blocks not found');
                    return;
                }

                const showEdit = edit.style.display === 'none' || edit.style.display === '';

                view.style.display = showEdit ? 'none' : 'block';
                edit.style.display = showEdit ? 'block' : 'none';

                // 🔥 ВАЖНО: принудительный reflow
                edit.offsetHeight;

                if (showEdit) {
                    edit.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        </script>

    </div>
    <script>
        function toggleBlock(id) {
            const el = document.getElementById(id);
            if (!el) {
                console.warn('Block not found:', id);
                return;
            }

            el.style.display = (el.style.display === 'none' || el.style.display === '')
                ? 'block'
                : 'none';
        }
    </script>
    <script>
        (function () {
            const toggle = document.getElementById('extendedKinshipToggle');
            if (!toggle) return;

            const STORAGE_KEY = 'kinship_extended';

            // 1️⃣ при загрузке страницы — синхронизируем с localStorage
            const saved = localStorage.getItem(STORAGE_KEY);

            if (saved !== null) {
                const shouldBeChecked = saved === '1';
                if (toggle.checked !== shouldBeChecked) {
                    toggle.checked = shouldBeChecked;
                }
            }

            // 2️⃣ при клике — сохраняем и перезагружаем
            toggle.addEventListener('change', function () {
                const isChecked = toggle.checked;

                localStorage.setItem(STORAGE_KEY, isChecked ? '1' : '0');

                const url = new URL(window.location.href);

                if (isChecked) {
                    url.searchParams.set('extended', '1');
                } else {
                    url.searchParams.delete('extended');
                }

                window.location.href = url.toString();
            });
        })();
    </script>

@endsection
