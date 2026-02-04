@php
    /** @var \Illuminate\Support\Collection $marriageCandidates */
    $marriageCandidates = $marriageCandidates ?? collect();
@endphp


<div class="card mt-4" id="relationship-form">
    <div class="card-body">

        <h5 class="mb-1">➕ Новая связь</h5>
        <div class="text-muted mb-3" style="font-size:14px;">
            Выберите тип отношений и второго человека
        </div>

        <form method="POST" action="{{ route('couples.store', $person) }}">
            @csrf

            {{-- ТИП СВЯЗИ --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Тип связи</label>

                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="relation_type"
                           value="marriage"
                           id="rel_marriage"
                           checked
                           onchange="toggleDates()">
                    <label class="form-check-label" for="rel_marriage">
                        💍 <strong>Официальный брак</strong>
                        <div class="text-muted small">
                            Юридически оформленные отношения
                        </div>
                    </label>
                </div>

                <div class="form-check mt-2">
                    <input class="form-check-input"
                           type="radio"
                           name="relation_type"
                           value="civil"
                           id="rel_civil"
                           onchange="toggleDates()">
                    <label class="form-check-label" for="rel_civil">
                        🤝 <strong>Гражданский союз</strong>
                        <div class="text-muted small">
                            Жили вместе без регистрации
                        </div>
                    </label>
                </div>

                <div class="form-check mt-2">
                    <input class="form-check-input"
                           type="radio"
                           name="relation_type"
                           value="parents"
                           id="rel_parents"
                           onchange="toggleDates()">
                    <label class="form-check-label" for="rel_parents">
                        👶 <strong>Родители ребёнка</strong>
                        <div class="text-muted small">
                            Есть общий ребёнок, без союза
                        </div>
                    </label>
                </div>
            </div>

            {{-- ВТОРОЙ ЧЕЛОВЕК --}}
            <div class="mb-3">
                <label class="form-label">Второй человек</label>
                <select name="spouse_id" class="form-select" required>
                    <option value="">Выберите человека</option>
                    @foreach($marriageCandidates as $candidate)
                        <option value="{{ $candidate->id }}">
                            {{ $candidate->last_name }} {{ $candidate->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ДАТЫ --}}
            <div id="relation-dates">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Начало отношений</label>
                        <input type="date"
                               name="married_at"
                               class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Окончание</label>
                        <input type="date"
                               name="divorced_at"
                               class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary">
                    💾 Создать связь
                </button>

                <button type="button"
                        class="btn btn-outline-secondary"
                        onclick="document.getElementById('relationship-form').remove()">
                    Отмена
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function toggleDates() {
        const parents = document.getElementById('rel_parents').checked;
        document.getElementById('relation-dates').style.display = parents ? 'none' : 'block';
    }

    document.addEventListener('DOMContentLoaded', toggleDates);
</script>
