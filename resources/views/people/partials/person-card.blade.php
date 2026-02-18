
@once
    @push('styles')
        <style>
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

            .person-phrase {
                font-size: 12px;
                font-style: italic;
                color: #6b7280;
                margin-top: 4px;
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

            .root-person {
                border: 2px solid #f59e0b !important;
                box-shadow: 0 0 0 3px rgba(245,158,11,.25);
            }
        </style>
    @endpush
@endonce


@php
    $fullName = trim(
        ($person->last_name ?? '') . ' ' .
        ($person->first_name ?? '') . ' ' .
        ($person->patronymic ?? '')
    );

    $birthYear = $person->birth_date
        ? \Carbon\Carbon::parse($person->birth_date)->year
        : null;

    $deathYear = $person->death_date
        ? \Carbon\Carbon::parse($person->death_date)->year
        : null;

    if ($birthYear) {
        $lifeLine = $deathYear
            ? "$birthYear — $deathYear"
            : "род. $birthYear";
    } else {
        $lifeLine = null;
    }

    $isRoot = isset($rootId) && $person->id === $rootId;
@endphp

<div class="person-card
    {{ $person->death_date ? 'dead' : 'alive' }}
    {{ $isRoot ? 'root-person' : '' }}"
     data-name="{{ mb_strtolower($fullName) }}"
     data-gender="{{ $person->gender }}"
     data-war="{{ $person->is_war_participant ? '1' : '0' }}"
     data-life="{{ $person->death_date ? 'dead' : 'alive' }}">

    {{-- Ссылка --}}
    <a href="{{ route('people.show', $person) }}" class="person-link"></a>

    {{-- Кнопка дерева --}}
    <button class="tree-btn"
            onclick="event.stopPropagation(); window.location='{{ route('tree.view', $person) }}'">
        🌳
    </button>

    {{-- Бейджи --}}
    <div class="badges">

        @if($isRoot)
            <div class="badge badge-root">👑 Родоначальник</div>
        @endif

        @if($person->is_war_participant)
            <div class="badge badge-war">🎖 ВОВ</div>
        @endif

        @if($person->death_date)
            <div class="badge badge-dead">🕯 Умер</div>
        @else
            <div class="badge badge-alive">❤️ Жив</div>
        @endif

    </div>

    {{-- Фото --}}
    <div class="person-photo">
        <img src="{{ $person->photo
            ? asset('storage/'.$person->photo)
            : asset('storage/people/placepeople.png') }}">
    </div>

    {{-- Имя --}}
    <div class="person-name">
        {{ $fullName }}
    </div>

    {{-- Годы жизни --}}
    @if($lifeLine)
        <div class="person-life">
            {{ $lifeLine }}
            @if($person->life_phrase)
                <div class="person-phrase">
                    {{ $person->life_phrase }}
                </div>
            @endif
        </div>
    @endif

</div>
