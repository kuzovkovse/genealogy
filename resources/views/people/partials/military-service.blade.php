<div class="card mb-4">
    <div class="card-body">

        {{-- Заголовок --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">🪖 Участие в войнах</h5>

            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm"
                        type="button"
                        onclick="toggleMilitaryEdit()">
                    ✏️ Редактировать
                </button>

                <button class="btn btn-outline-primary btn-sm"
                        type="button"
                        onclick="toggleAddMilitary()">
                    ➕ Добавить службу
                </button>
            </div>
        </div>

        {{-- =======================
         | READ-ONLY
         ======================= --}}
        @if($person->militaryServices->count())
            <div id="military-readonly">
                @foreach($person->militaryServices as $service)
                    <div class="border rounded p-3 mb-3 bg-light">

                        <div class="fw-bold">
                            {{ $service->warLabel() ?? 'Военная служба' }}
                        </div>

                        <div class="text-muted small">
                            {{ $service->rank }}
                            @if($service->unit)
                                — {{ $service->unit }}
                            @endif
                        </div>

                        @if($service->draft_year || $service->service_end)
                            <div class="small mt-1">
                                Служба: {{ $service->draft_year ?? '—' }} — {{ $service->service_end ?? '—' }}
                            </div>
                        @endif

                        {{-- Документы --}}
                        <div class="mt-3">
                            <div class="fw-semibold small mb-1">📎 Документы</div>

                            @if($service->documents && $service->documents->count())
                                <ul class="list-unstyled small mb-0">
                                    @foreach($service->documents as $doc)
                                        <li class="mb-1">
                                            @if($doc->isImage())
                                                🖼
                                            @else
                                                📄
                                            @endif

                                            <a href="{{ $doc->url() }}" target="_blank">
                                                {{ $doc->title ?? 'Документ' }}
                                            </a>

                                            @if($doc->document_date)
                                                <span class="text-muted">
                                                    — {{ \Carbon\Carbon::parse($doc->document_date)->format('d.m.Y') }}
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-muted small">
                                    Документы не добавлены
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- =======================
         | РЕДАКТИРОВАНИЕ
         ======================= --}}
        <div id="military-edit" style="display:none">

            @foreach($person->militaryServices as $service)
                <form method="POST"
                      action="{{ route('military.update', $service) }}"
                      enctype="multipart/form-data"
                      class="border rounded p-3 mb-4">

                    @csrf
                    @method('PATCH')

                    <h6 class="mb-3">✏️ Редактирование службы</h6>

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
                    </div>

                    {{-- 📎 ДОКУМЕНТЫ --}}
                    <div class="mt-4">
                        <div class="fw-semibold small mb-2">📎 Документы службы</div>

                        {{-- Список документов --}}
                        @if($service->documents && $service->documents->count())
                            <ul class="list-unstyled small mb-3">
                                @foreach($service->documents as $doc)
                                    <li class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            {{ $doc->title ?? 'Документ' }}
                                            @if($doc->document_date)
                                                <span class="text-muted">
                                                    ({{ \Carbon\Carbon::parse($doc->document_date)->format('d.m.Y') }})
                                                </span>
                                            @endif
                                        </div>

                                        <form method="POST"
                                              action="{{ route('military.documents.destroy', $doc) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                Удалить
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- Добавление документа --}}
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small">Название документа</label>
                                <input name="title"
                                       class="form-control form-control-sm"
                                       placeholder="Например: Приказ о награждении">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small">Дата документа</label>
                                <input type="date"
                                       name="document_date"
                                       class="form-control form-control-sm">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label small">Файл (PDF или изображение)</label>
                                <input type="file"
                                       name="file"
                                       accept="image/*,.pdf"
                                       class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-primary btn-sm">💾 Сохранить</button>

                        <form method="POST"
                              action="{{ route('military.destroy', $service) }}"
                              onsubmit="return confirm('Удалить службу?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">
                                🗑 Удалить службу
                            </button>
                        </form>
                    </div>
                </form>
            @endforeach
        </div>

        {{-- =======================
         | ДОБАВЛЕНИЕ
         ======================= --}}
        <div id="military-add" style="display:none">
            <form method="POST"
                  action="{{ route('military.store', $person) }}"
                  class="border rounded p-3 mt-3">

                @csrf

                <h6 class="mb-3">➕ Добавить службу</h6>

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
                        <textarea name="awards" class="form-control" rows="2" placeholder="Награды"></textarea>
                    </div>
                </div>

                <button class="btn btn-outline-primary btn-sm mt-3">
                    ➕ Добавить
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    function toggleMilitaryEdit() {
        const el = document.getElementById('military-edit');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function toggleAddMilitary() {
        const el = document.getElementById('military-add');
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
</script>
