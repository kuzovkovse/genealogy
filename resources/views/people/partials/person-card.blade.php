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
