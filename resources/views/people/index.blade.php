@extends('layouts.app')

@section('title', 'Люди')

@section('content')

    {{-- Заголовок --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Люди</h1>

        @can('create', \App\Models\Person::class)
            <a href="{{ route('people.create') }}" class="btn btn-primary">
                ➕ Добавить человека
            </a>
        @endcan
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

        /* статус */
        .person-card.alive {
            border-color: #86efac;
        }

        .person-card.dead {
            border-color: #d1d5db;
            filter: grayscale(35%) contrast(0.95);
        }

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

        /* overlay */
        .person-link {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        /* кнопка дерева */
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

        /* бейджи */
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
            font-weight: 500;
            background: rgba(255,255,255,.9);
            box-shadow: 0 2px 6px rgba(0,0,0,.12);
            white-space: nowrap;
        }

        .badge-war { color: #92400e; }
        .badge-alive { color: #065f46; }
        .badge-dead { color: #7f1d1d; }
    </style>

    {{-- Поколения --}}
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
                        $photo = $person->photo
                            ? asset('storage/'.$person->photo)
                            : asset('storage/people/placepeople.png');

                        $fullName = trim(
                            ($person->last_name ?? '').' '.
                            ($person->first_name ?? '').' '.
                            ($person->patronymic ?? '')
                        );

                        $birthYear = $person->birth_date ? \Carbon\Carbon::parse($person->birth_date)->year : null;
                        $deathYear = $person->death_date ? \Carbon\Carbon::parse($person->death_date)->year : null;

                        $lifeLine = $birthYear
                            ? ($deathYear ? "$birthYear — $deathYear" : "род. $birthYear")
                            : null;
                    @endphp

                    <div class="person-card {{ $person->death_date ? 'dead' : 'alive' }}">

                        <a href="{{ route('people.show', $person) }}" class="person-link"></a>

                        <button class="tree-btn"
                                onclick="event.stopPropagation(); window.location='{{ route('tree.view', $person) }}'"
                                title="Показать в древе">🌳</button>

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
                            <img src="{{ $photo }}" alt="{{ $fullName }}">
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

@endsection
