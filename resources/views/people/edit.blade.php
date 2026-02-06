@extends('layouts.app')

@section('title', 'Редактирование')

@section('content')
    <div class="container">

        <h1 class="mb-4">Редактирование</h1>

        {{-- ================== ФОТО ЧЕЛОВЕКА ================== --}}
        <div class="card mb-4">
            <div class="card-body">

                <h5 class="mb-3">Фотография</h5>

                <div class="d-flex align-items-center gap-4 flex-wrap">

                    {{-- Текущее фото --}}
                    <div>
                        <img
                            src="{{ $person->photo
                            ? asset('storage/'.$person->photo)
                            : route('avatar', [
                                'name' => mb_substr($person->first_name,0,1).mb_substr($person->last_name,0,1),
                                'gender' => $person->gender
                            ])
                        }}"
                            style="
                            width:120px;
                            height:120px;
                            object-fit:cover;
                            border-radius:50%;
                            border:4px solid #e5e7eb;
                        "
                        >
                    </div>

                    {{-- Форма загрузки --}}
                    <div>
                        <form method="POST"
                              action="{{ route('people.photo.update', $person) }}"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="mb-2">
                                <input
                                    type="file"
                                    name="photo"
                                    accept="image/*"
                                    class="form-control"
                                    required
                                >
                            </div>

                            <button class="btn btn-outline-primary btn-sm">
                                📷 Заменить фото
                            </button>
                        </form>

                        <div class="text-muted mt-2" style="font-size:12px;">
                            JPG / PNG, до 2 МБ
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ================== ОСНОВНЫЕ ДАННЫЕ ================== --}}
        <form method="POST" action="{{ route('people.update', $person) }}">
            @csrf
            @method('PATCH')

            {{-- Имя --}}
            <div class="mb-3">
                <label class="form-label">Имя *</label>
                <input name="first_name"
                       class="form-control"
                       value="{{ old('first_name', $person->first_name) }}"
                       required>
            </div>

            {{-- Отчество --}}
            <div class="mb-3">
                <label class="form-label">Отчество</label>
                <input name="patronymic"
                       class="form-control"
                       value="{{ old('patronymic', $person->patronymic) }}"
                       placeholder="Необязательно">
            </div>

            {{-- Фамилия --}}
            <div class="mb-3">
                <label class="form-label">Фамилия</label>
                <input name="last_name"
                       class="form-control"
                       value="{{ old('last_name', $person->last_name) }}">
            </div>

            {{-- Фамилия при рождении --}}
            <div class="mb-3"
                 id="birth-last-name-block"
                 style="{{ $person->gender === 'female' || $person->birth_last_name ? '' : 'display:none;' }}">

                <label class="form-label">
                    Фамилия при рождении
                    <small class="text-muted">(девичья)</small>
                </label>

                <input
                    name="birth_last_name"
                    id="birth_last_name"
                    class="form-control"
                    value="{{ old('birth_last_name', $person->birth_last_name) }}"
                    placeholder="Если отличается от текущей"
                >
            </div>

            {{-- Пол --}}
            <div class="mb-3">
                <label class="form-label">Пол</label>
                <select name="gender" class="form-select">
                    <option value="">—</option>
                    <option value="male" @selected($person->gender === 'male')>Мужской</option>
                    <option value="female" @selected($person->gender === 'female')>Женский</option>
                </select>
            </div>

            {{-- Дата рождения --}}
            <div class="mb-3">
                <label class="form-label">
                    Дата рождения
                    <small class="text-muted">(YYYY-MM-DD или ~YYYY)</small>
                </label>
                <input name="birth_date"
                       class="form-control"
                       placeholder="Напр. 1988-09-29 или ~1988"
                       value="{{ old('birth_date', $person->birth_date) }}">
            </div>

            {{-- Дата смерти --}}
            <div class="mb-3">
                <label class="form-label">
                    Дата смерти
                    <small class="text-muted">(YYYY-MM-DD или YYYY)</small>
                </label>

                <input name="death_date"
                       class="form-control"
                       placeholder="Напр. 2015 или 2015-02-12"
                       value="{{ old('death_date', $person->death_date) }}">

                <small class="text-muted">
                    Если указана — человек считается умершим
                </small>
            </div>

            {{-- ================== ВОЕННЫЙ СТАТУС ================== --}}
            <div class="card mb-4 mt-4">
                <div class="card-body">

                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            id="is_war_participant"
                            name="is_war_participant"
                            value="1"
                            @checked(old('is_war_participant', $person->is_war_participant))
                        >

                        <label class="form-check-label fw-semibold"
                               for="is_war_participant">
                            🪖 Участник войн
                        </label>
                    </div>

                    <div class="text-muted mt-2" style="font-size:13px;">
                        Включите, если человек участвовал в военных действиях
                        (Великая Отечественная, Первая мировая и др.)
                    </div>

                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary">💾 Сохранить</button>
                <a href="{{ route('people.show', $person) }}" class="btn btn-outline-secondary">
                    Отмена
                </a>
            </div>

        </form>
    </div>

    <script>
        function toggleBirthLastNameEdit() {
            const genderSelect = document.querySelector('select[name="gender"]');
            const block = document.getElementById('birth-last-name-block');
            const input = document.getElementById('birth_last_name');

            if (!genderSelect || !block) return;

            if (genderSelect.value === 'female') {
                block.style.display = 'block';
            } else {
                block.style.display = 'none';
                if (input) input.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const genderSelect = document.querySelector('select[name="gender"]');
            if (!genderSelect) return;

            genderSelect.addEventListener('change', toggleBirthLastNameEdit);
        });
    </script>

@endsection
