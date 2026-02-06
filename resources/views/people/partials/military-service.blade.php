<div class="card mb-4">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">🪖 Участие в войнах</h5>

            <button type="button"
                    class="btn btn-outline-primary btn-sm"
                    onclick="toggleAddMilitaryForm()">
                ➕ Добавить службу
            </button>
        </div>

        {{-- =======================
         | READ-ONLY (КРАСИВЫЙ)
         ======================= --}}
        @if($person->militaryServices->count())
            <div class="d-flex flex-column gap-3 mb-4">

                @foreach($person->militaryServices as $service)
                    <div class="p-3 rounded border bg-white shadow-sm">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                {{-- Война --}}
                                <div class="fw-semibold fs-6">
                                    🎖 {{ $service->warLabel() }}
                                </div>

                                {{-- Звание + часть --}}
                                <div class="text-muted mt-1">
                                    🪖 {{ $service->rank ?: 'Военная служба' }}
                                    @if($service->unit)
                                        <span class="text-secondary">— {{ $service->unit }}</span>
                                    @endif
                                </div>

                                {{-- Годы --}}
                                @if($service->draft_year || $service->service_end)
                                    <div class="small mt-1 text-secondary">
                                        ⏳
                                        {{ $service->draft_year ?? '—' }}
                                        —
                                        {{ $service->service_end ?? '—' }}
                                    </div>
                                @endif

                                {{-- Награды --}}
                                @if($service->awards)
                                    <div class="small mt-2">
                                        🏅 {{ $service->awards }}
                                    </div>
                                @endif

                                {{-- Гибель --}}
                                @if($service->is_killed)
                                    <div class="small mt-2 text-danger">
                                        ✝ Погиб
                                        @if($service->killed_date)
                                            <span class="text-muted">
                                                ({{ $service->killed_date->format('d.m.Y') }})
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Кнопка редактирования --}}
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    onclick="toggleEditForm({{ $service->id }})">
                                ✏️
                            </button>
                        </div>

                        {{-- =======================
                         | EDIT FORM (СКРЫТА)
                         ======================= --}}
                        <div id="edit-form-{{ $service->id }}"
                             class="mt-3"
                             style="display:none;">

                            <form method="POST"
                                  action="{{ route('military.update', $service) }}"
                                  class="border rounded p-3 bg-light">

                                @csrf
                                @method('PATCH')

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">Война / конфликт *</label>
                                        <select name="war_type" class="form-select" required>
                                            <option value="">—</option>
                                            <option value="ww2" @selected($service->war_type === 'ww2')>Великая Отечественная</option>
                                            <option value="ww1" @selected($service->war_type === 'ww1')>Первая мировая</option>
                                            <option value="afghanistan" @selected($service->war_type === 'afghanistan')>Афганистан</option>
                                            <option value="chechnya" @selected($service->war_type === 'chechnya')>Чечня</option>
                                            <option value="other" @selected($service->war_type === 'other')>Другое</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Звание</label>
                                        <input name="rank" class="form-control" value="{{ $service->rank }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Воинская часть</label>
                                        <input name="unit" class="form-control" value="{{ $service->unit }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Год призыва</label>
                                        <input name="draft_year" type="number" class="form-control" value="{{ $service->draft_year }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Год окончания</label>
                                        <input name="service_end" type="number" class="form-control" value="{{ $service->service_end }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Награды</label>
                                        <textarea name="awards" class="form-control" rows="2">{{ $service->awards }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input kill-toggle"
                                                   type="checkbox"
                                                   data-target="killed-{{ $service->id }}"
                                                   name="is_killed"
                                                   value="1"
                                                @checked($service->is_killed)>
                                            <label class="form-check-label">
                                                Погиб в ходе службы
                                            </label>
                                        </div>
                                    </div>

                                    <div id="killed-{{ $service->id }}"
                                         class="row g-3 mt-1"
                                         style="{{ $service->is_killed ? '' : 'display:none' }}">

                                        <div class="col-md-6">
                                            <label class="form-label">Дата гибели</label>
                                            <input type="date"
                                                   name="killed_date"
                                                   class="form-control"
                                                   value="{{ optional($service->killed_date)->format('Y-m-d') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Место захоронения</label>
                                            <input name="burial_place"
                                                   class="form-control"
                                                   value="{{ $service->burial_place }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-primary btn-sm">💾 Сохранить</button>

                                    <form method="POST"
                                          action="{{ route('military.destroy', $service) }}"
                                          onsubmit="return confirm('Удалить запись?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">
                                            🗑 Удалить
                                        </button>
                                    </form>
                                </div>
                            </form>
                        </div>

                    </div>
                @endforeach

            </div>
        @else
            <div class="text-muted mb-4">
                Информация о военной службе пока не добавлена.
            </div>
        @endif

        {{-- =======================
         | ADD FORM (СКРЫТА)
         ======================= --}}
        <div id="add-military-form" style="display:none;">
            <form method="POST"
                  action="{{ route('military.store', $person) }}"
                  class="border rounded p-3 bg-light">

                @csrf

                <h6 class="mb-3">➕ Новая запись службы</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="war_type" class="form-select" required>
                            <option value="">Война / конфликт *</option>
                            <option value="ww2">Великая Отечественная</option>
                            <option value="ww1">Первая мировая</option>
                            <option value="afghanistan">Афганистан</option>
                            <option value="chechnya">Чечня</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <input name="rank" class="form-control" placeholder="Звание">
                    </div>

                    <div class="col-md-6">
                        <input name="unit" class="form-control" placeholder="Воинская часть">
                    </div>

                    <div class="col-md-3">
                        <input name="draft_year" type="number" class="form-control" placeholder="Год призыва">
                    </div>

                    <div class="col-md-3">
                        <input name="service_end" type="number" class="form-control" placeholder="Год окончания">
                    </div>

                    <div class="col-12">
                        <textarea name="awards"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Награды"></textarea>
                    </div>
                </div>

                <button class="btn btn-primary btn-sm mt-3">💾 Сохранить</button>
            </form>
        </div>

    </div>
</div>

<script>
    function toggleAddMilitaryForm() {
        const form = document.getElementById('add-military-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function toggleEditForm(id) {
        const el = document.getElementById('edit-form-' + id);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.querySelectorAll('.kill-toggle').forEach(cb => {
        cb.addEventListener('change', () => {
            const target = document.getElementById(cb.dataset.target);
            if (target) target.style.display = cb.checked ? 'flex' : 'none';
        });
    });
</script>
