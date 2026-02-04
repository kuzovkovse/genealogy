@extends('layouts.public')

@section('title', $person->last_name.' '.$person->first_name)

@section('content')
    @php
        $isMemorial = (bool) $person->death_date;
    @endphp
    <div class="container py-5 {{ $isMemorial ? 'memorial' : '' }}">
        <style>
        .memorial img {
        filter: grayscale(100%);
        }

        .memorial .card {
        border: 1px dashed #d1d5db;
        background: #fafafa;
        }

        .memorial .life-years {
        color: #9ca3af;
        }

        .memorial-candle {
        margin-left: 6px;
        }
        </style>

    {{-- HERO --}}
        <div class="card mb-4">
            <div class="card-body text-center">

                <img
                    src="{{ $person->photo ? asset('storage/'.$person->photo) : route('avatar', ['name' => mb_substr($person->first_name,0,1).mb_substr($person->last_name,0,1), 'gender' => $person->gender]) }}"
                    class="rounded-circle mb-3 person-photo"
                    style="width:180px;height:180px;object-fit:cover;"
                >

                <h2 class="mb-1">
                    {{ $person->last_name }} {{ $person->first_name }}
                </h2>

                <div class="text-muted mb-2 life-years">
                    {{ $person->birth_date }}
                    —
                    {{ $person->death_date ?? 'н.в.' }}
                    @if($isMemorial)
                        <span class="memorial-candle">🕯</span>
                    @endif
                </div

            </div>
        </div>

        {{-- БИОГРАФИЯ --}}
        @if($person->biography)
            <div class="card mb-4">
                <div class="card-body">
                    <h4>История жизни</h4>
                    <div class="markdown">
                        {!! nl2br(e($person->biography)) !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- <БРАКИ+ДЕТИ> --}}
        @if($couples->count())
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Семья</h4>

                    @foreach($couples as $couple)
                        @php
                            $spouse = $couple->person_1_id === $person->id
                                ? $couple->person2
                                : $couple->person1;
                        @endphp

                        <div class="mb-3">
                            <strong>
                                {{ $spouse->last_name }} {{ $spouse->first_name }}
                            </strong>
                            <div class="text-muted small">
                                {{ $couple->married_at }} — {{ $couple->divorced_at ?? 'н.в.' }}
                            </div>

                            @if($couple->children->count())
                                <div class="mt-2">
                                    <small class="text-muted">Дети:</small>
                                    <ul>
                                        @foreach($couple->children as $child)
                                            <li>{{ $child->first_name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        {{-- РОДИТЕЛИ--}}
        @if($parentsCouple)
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Родители</h4>

                    <div class="d-flex gap-4">
                        @if($parentsCouple->person1)
                            <div>
                                <strong>Отец</strong><br>
                                {{ $parentsCouple->person1->last_name }}
                                {{ $parentsCouple->person1->first_name }}
                            </div>
                        @endif

                        @if($parentsCouple->person2)
                            <div>
                                <strong>Мать</strong><br>
                                {{ $parentsCouple->person2->last_name }}
                                {{ $parentsCouple->person2->first_name }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    @endif
@endsection
