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

    $lifeLine = $birthYear
        ? ($deathYear ? "$birthYear — $deathYear" : "род. $birthYear")
        : null;

    $isRoot = isset($rootId) && $person->id === $rootId;
@endphp


<div class="person-card {{ $isRoot ? 'root-person' : '' }}"
     data-name="{{ mb_strtolower($fullName) }}"
     data-gender="{{ $person->gender }}"
     data-war="{{ $person->is_war_participant ? '1' : '0' }}"
     data-life="{{ $person->death_date ? 'dead' : 'alive' }}">

    <a href="{{ route('people.show', $person) }}" class="stretched-link"></a>

    <div class="person-photo">

        <img src="{{ $person->photo
            ? asset('storage/'.$person->photo)
            : asset('storage/people/placepeople.png') }}"
             alt="{{ $fullName }}">

        <div class="person-badges">

            @if($isRoot)
                <span class="person-badge badge-root">👑 Родоначальник</span>
            @endif

            @if($person->is_war_participant)
                <span class="person-badge badge-war">🎖 ВОВ</span>
            @endif

            @if($person->death_date)
                <span class="person-badge badge-dead">🕯 Память</span>
            @else
                <span class="person-badge badge-alive">❤️ Жив</span>
            @endif

        </div>

        <div class="tree-button"
             onclick="event.stopPropagation(); window.location='{{ route('tree.view', $person) }}'">
            🧬
        </div>

        <div class="person-name-overlay">
            {{ $fullName }}
        </div>

    </div>

    <div class="person-meta">

        @if($lifeLine)
            <div class="person-years">
                {{ $lifeLine }}
            </div>
        @endif

        @if($person->life_phrase)
            <div class="person-phrase">
                {{ $person->life_phrase }}
            </div>
        @endif

    </div>

</div>
