@extends('layouts.app')

@section('title', 'Люди')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Люди</h1>

        <a href="{{ route('people.create') }}" class="btn btn-primary">
            ➕ Добавить человека
        </a>
    </div>

    <style>
        .people-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .person-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .person-card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            transform: translateY(-3px);
        }

        .person-photo {
            width: 100%;
            height: 220px;
            background: #f3f4f6;
        }

        .person-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* 🔑 КЛЮЧЕВО */
            display: block;
        }

        .person-name {
            padding: 12px 14px;
            font-weight: 600;
            text-align: center;
            border-top: 1px solid #eee;
            background: #fff;
        }
    </style>

    <div class="people-grid">
        @forelse($people as $person)

            @php
                $photo = $person->photo
                    ? asset('storage/' . $person->photo)
                    : asset('storage/people/placepeople.png');
            @endphp

            <a href="{{ route('people.show', $person) }}" class="person-card">
                <div class="person-photo">
                    <img src="{{ $photo }}" alt="{{ $person->last_name }} {{ $person->first_name }}">
                </div>

                <div class="person-name">
                    {{ $person->last_name }} {{ $person->first_name }}
                </div>
            </a>

        @empty
            <p>Пока нет ни одного человека</p>
        @endforelse
    </div>

@endsection
