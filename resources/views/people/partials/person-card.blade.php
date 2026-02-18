
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
                width: 36px;
                height: 36px;
                border-radius: 50%;
                border: none;

                background: rgba(255,255,255,.95);
                color: #374151;

                display: flex;
                align-items: center;
                justify-content: center;

                box-shadow: 0 4px 12px rgba(0,0,0,.15);
                transition: all .2s ease;
                cursor: pointer;
            }

            .tree-btn:hover {
                background: #0d6efd;
                color: white;
                transform: scale(1.05);
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

            .card.person-card {
                transition: transform .25s ease, box-shadow .25s ease;
            }

            .card.person-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 25px 50px rgba(0,0,0,.12);
            }

            /* Лёгкое увеличение фото */
            .card.person-card .card-img-top {
                transition: transform .3s ease;
            }

            .card.person-card:hover .card-img-top {
                transform: scale(1.04);
            }

            .generation-content {
                overflow: hidden;
                transition: all .3s ease;
            }

            .generation-content.collapsed {
                opacity: 0;
                transform: translateY(-5px);
                height: 0;
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


<div class="card person-card h-100 position-relative
    {{ $isRoot ? 'border-warning' : '' }}"
     data-name="{{ mb_strtolower($fullName) }}"
     data-gender="{{ $person->gender }}"
     data-war="{{ $person->is_war_participant ? '1' : '0' }}"
     data-life="{{ $person->death_date ? 'dead' : 'alive' }}">

    {{-- CLICKABLE OVERLAY --}}
    <a href="{{ route('people.show', $person) }}"
       class="stretched-link"></a>

    {{-- BADGES --}}
    <div class="position-absolute top-0 start-0 p-2 d-flex flex-column gap-1" style="z-index: 2">

        @if($isRoot)
            <span class="badge bg-warning-lt text-warning">
                👑 Родоначальник
            </span>
        @endif

        @if($person->is_war_participant)
            <span class="badge bg-orange-lt text-orange">
                🎖 ВОВ
            </span>
        @endif

        @if($person->death_date)
            <span class="badge bg-secondary-lt text-secondary">
                🕯 Умер
            </span>
        @else
            <span class="badge bg-green-lt text-green">
                ❤️ Жив
            </span>
        @endif

    </div>

    {{-- TREE BUTTON --}}
    <div class="position-absolute top-0 end-0 p-2" style="z-index: 3">
        <button type="button"
                class="tree-btn"
                data-bs-toggle="tooltip"
                data-bs-placement="left"
                title="Открыть дерево"
                onclick="event.stopPropagation(); window.location='{{ route('tree.view', $person) }}'">

            <svg xmlns="http://www.w3.org/2000/svg"
                 width="16" height="16"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <circle cx="12" cy="5" r="2"/>
                <circle cx="6" cy="19" r="2"/>
                <circle cx="18" cy="19" r="2"/>
                <path d="M12 7v4"/>
                <path d="M6 17l6-6 6 6"/>
            </svg>

        </button>
    </div>

    {{-- IMAGE --}}
    <img src="{{ $person->photo
        ? asset('storage/'.$person->photo)
        : asset('storage/people/placepeople.png') }}"
         class="card-img-top"
         style="height: 220px; object-fit: cover;
                {{ $person->death_date ? 'filter: grayscale(60%);' : '' }}">

    {{-- BODY --}}
    <div class="card-body text-center">

        <h4 class="card-title mb-1">
            {{ $fullName }}
        </h4>

        @if($lifeLine)
            <div class="text-muted small">
                {{ $lifeLine }}
            </div>
        @endif

        @if($person->life_phrase)
            <div class="mt-2 text-muted small fst-italic">
                {{ $person->life_phrase }}
            </div>
        @endif

    </div>

</div>
