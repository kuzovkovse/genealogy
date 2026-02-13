@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <h2 class="mb-4">Редактирование связи</h2>

                        <form method="POST" action="{{ route('couples.update', $couple) }}">
                            @csrf
                            @method('PUT')

                            {{-- Тип связи --}}
                            <div class="mb-3">
                                <label class="form-label">Тип связи</label>
                                <select name="relation_type" class="form-select">
                                    <option value="marriage" @selected($couple->relation_type === 'marriage')>
                                        Официальный брак
                                    </option>
                                    <option value="civil" @selected($couple->relation_type === 'civil')>
                                        Гражданский союз
                                    </option>
                                    <option value="parents" @selected($couple->relation_type === 'parents')>
                                        Родители ребёнка
                                    </option>
                                </select>
                            </div>

                            {{-- Дата начала --}}
                            <div class="mb-3">
                                <label class="form-label">Дата начала</label>
                                <input type="text"
                                       id="started_at"
                                       name="started_at"
                                       class="form-control"
                                       placeholder="дд.мм.гггг"
                                       value="{{ old('started_at', $couple->started_at ? \Carbon\Carbon::parse($couple->started_at)->format('d.m.Y') : '') }}">
                            </div>

                            {{-- Дата окончания --}}
                            <div class="mb-3">
                                <label class="form-label">Дата окончания</label>
                                <input type="text"
                                       id="ended_at"
                                       name="ended_at"
                                       class="form-control"
                                       placeholder="дд.мм.гггг"
                                       value="{{ old('ended_at', $couple->ended_at ? \Carbon\Carbon::parse($couple->ended_at)->format('d.m.Y') : '') }}">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('people.show', $couple->person_1_id) }}"
                                   class="btn btn-outline-secondary">
                                    ← Назад
                                </a>

                                <button class="btn btn-primary">
                                    💾 Сохранить
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- Flatpickr (календарь) --}}
    {{-- ========================= --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js"></script>

    <script>
        flatpickr("#started_at", {
            dateFormat: "d.m.Y",
            allowInput: true,
            locale: "ru"
        });

        flatpickr("#ended_at", {
            dateFormat: "d.m.Y",
            allowInput: true,
            locale: "ru"
        });
    </script>

@endsection
