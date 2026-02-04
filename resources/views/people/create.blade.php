@extends('layouts.app')

@section('title', 'Добавить человека')

@section('content')
    <h1 class="mb-4">Добавить человека</h1>

    <form method="POST" action="{{ route('people.store') }}" enctype="multipart/form-data">
    @csrf
        <div class="mb-3">
            <label class="form-label">Фото</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Имя *</label>
                <input name="first_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Фамилия</label>
                <input name="last_name" class="form-control">
            </div>

            <div class="mb-3" id="birth-last-name-block" style="display:none;">
                <label class="form-label">
                    Фамилия при рождении
                    <small class="text-muted">(девичья)</small>
                </label>
                <input
                    name="birth_last_name"
                    id="birth_last_name"
                    class="form-control"
                    placeholder="Если отличается от текущей"
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Отчество</label>
                <input name="patronymic" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Пол</label>
                <select name="gender" class="form-select">
                    <option value="">—</option>
                    <option value="male">Мужской</option>
                    <option value="female">Женский</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Дата рождения</label>
                <input type="date" name="birth_date" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Место рождения</label>
                <input name="birth_place" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Биография</label>
                <textarea name="biography" class="form-control" rows="4"></textarea>
            </div>
        </div>

        <div class="card-footer text-end">
            <button class="btn btn-primary">Сохранить</button>
        </div>

        <script>
            function toggleBirthLastName() {
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

                toggleBirthLastName();
                genderSelect.addEventListener('change', toggleBirthLastName);
            });
        </script>


    </form>
@endsection
