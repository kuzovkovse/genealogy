<div class="memorial-card">

    {{-- ===============================
         UPDATE FORM
    ================================ --}}
    <form method="POST" action="{{ route('people.memorial.update', $person) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label">📍 Кладбище</label>
            <input type="text"
                   name="burial_cemetery"
                   class="form-control"
                   value="{{ old('burial_cemetery', $person->burial_cemetery) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Город / населённый пункт</label>
            <input type="text"
                   name="burial_city"
                   class="form-control"
                   value="{{ old('burial_city', $person->burial_city) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">🗂 Участок, ряд, место</label>
            <input type="text"
                   name="burial_place"
                   class="form-control"
                   value="{{ old('burial_place', $person->burial_place) }}">
        </div>

        <div class="mb-4">
            <label class="form-label">🧭 Как найти могилу</label>
            <textarea name="burial_description"
                      class="form-control"
                      rows="3">{{ old('burial_description', $person->burial_description) }}</textarea>
            <div class="form-text">
                Так, как вы бы объяснили близкому человеку
            </div>
        </div>

        <details class="mb-4">
            <summary class="text-muted" style="cursor:pointer;">
                🗺 Координаты (необязательно)
            </summary>

            <div class="row mt-3">
                <div class="col-md-6">
                    <input name="burial_lat"
                           class="form-control"
                           placeholder="Широта"
                           value="{{ old('burial_lat', $person->burial_lat) }}">
                </div>
                <div class="col-md-6">
                    <input name="burial_lng"
                           class="form-control"
                           placeholder="Долгота"
                           value="{{ old('burial_lng', $person->burial_lng) }}">
                </div>
            </div>
        </details>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                💾 Сохранить
            </button>

            <button type="button"
                    class="btn btn-outline-secondary"
                    onclick="toggleMemorialEdit()">
                Отмена
            </button>
        </div>
    </form>

    {{-- ===============================
         DELETE FORM (ОТДЕЛЬНАЯ)
    ================================ --}}
    @if($person->burial_cemetery || $person->burial_city)
        <form method="POST"
              action="{{ route('people.memorial.destroy', $person) }}"
              class="mt-3"
              onsubmit="return confirm('Удалить место памяти?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-outline-danger btn-sm">
                🗑 Удалить место памяти
            </button>
        </form>
    @endif

</div>
