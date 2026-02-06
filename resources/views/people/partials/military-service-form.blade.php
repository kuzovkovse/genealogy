<form method="POST"
      action="{{ $service
            ? route('military.update', $service)
            : route('military.store', $person) }}"
      class="border rounded-3 p-3 mb-3">

    @csrf
    @if($service)
        @method('PATCH')
    @endif

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Война / период</label>
            <input name="war_name"
                   class="form-control"
                   value="{{ old('war_name', $service->war_name ?? '') }}"
                   placeholder="Великая Отечественная война">
        </div>

        <div class="col-md-3">
            <label class="form-label">Год призыва</label>
            <input name="draft_year"
                   type="number"
                   class="form-control"
                   value="{{ old('draft_year', $service->draft_year ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Год окончания</label>
            <input name="service_end_year"
                   type="number"
                   class="form-control"
                   value="{{ old('service_end_year', $service->service_end_year ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Звание</label>
            <input name="rank"
                   class="form-control"
                   value="{{ old('rank', $service->rank ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Воинская часть</label>
            <input name="unit"
                   class="form-control"
                   value="{{ old('unit', $service->unit ?? '') }}">
        </div>

        <div class="col-12">
            <label class="form-label">Награды</label>
            <textarea name="awards"
                      class="form-control"
                      rows="2">{{ old('awards', $service->awards ?? '') }}</textarea>
        </div>

        <div class="col-12 form-check mt-2">
            <input type="checkbox"
                   class="form-check-input"
                   name="was_killed"
                   value="1"
                   @checked(old('was_killed', $service->was_killed ?? false))
                   onchange="this.closest('form').querySelector('.death-fields').style.display = this.checked ? 'block' : 'none'">
            <label class="form-check-label">
                Погиб на войне
            </label>
        </div>

        <div class="death-fields mt-3"
             style="{{ old('was_killed', $service->was_killed ?? false) ? '' : 'display:none;' }}">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Дата гибели</label>
                    <input name="death_date"
                           type="date"
                           class="form-control"
                           value="{{ old('death_date', optional($service?->death_date)->format('Y-m-d')) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Место захоронения</label>
                    <input name="burial_place"
                           class="form-control"
                           value="{{ old('burial_place', $service->burial_place ?? '') }}">
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary btn-sm">💾 Сохранить</button>

        @if($service)
            <form method="POST"
                  action="{{ route('military.destroy', $service) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">
                    🗑 Удалить
                </button>
            </form>
        @endif
    </div>

</form>
