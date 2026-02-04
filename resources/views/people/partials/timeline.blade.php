@if($timeline->isNotEmpty())
    <div class="timeline-card mb-5">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">⏳ Хронология жизни</h3>

            <div class="d-flex gap-2">
                <button type="button"
                        class="btn btn-sm btn-outline-secondary"
                        onclick="toggleTimeline()">
                    ⬍
                </button>

                <button type="button"
                        class="btn btn-sm btn-outline-primary"
                        onclick="toggleAddEvent()">
                    ➕ Добавить событие
                </button>
            </div>
        </div>

        {{-- ADD EVENT FORM --}}
        <div id="add-event-form"
             class="card mb-3"
             style="display:none;">
            <div class="card-body">

                <form method="POST"
                      action="{{ route('events.store', $person) }}">
                    @csrf

                    <div class="row g-2 mb-2">
                        <div class="col-md-3">
                            <input type="date"
                                   name="event_date"
                                   class="form-control form-control-sm"
                                   required>
                        </div>

                        <div class="col-md-2">
                            <input type="text"
                                   name="icon"
                                   class="form-control form-control-sm"
                                   placeholder="📌">
                        </div>

                        <div class="col-md-7">
                            <input type="text"
                                   name="title"
                                   class="form-control form-control-sm"
                                   placeholder="Название события"
                                   required>
                        </div>
                    </div>

                    <textarea name="description"
                              class="form-control form-control-sm mb-2"
                              rows="2"
                              placeholder="Описание (необязательно)"></textarea>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary">💾 Добавить</button>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick="toggleAddEvent()">
                            Отмена
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- TIMELINE --}}
        <div id="timeline-body">
            <div class="timeline">

                @foreach($timeline as $event)
                    @php
                        $isSystem = empty($event['id']);
                        $eventId = $event['id'] ?? 'sys-' . md5($event['title'].$event['date']);
                    @endphp

                    <div class="timeline-item">
                        <div class="timeline-line"></div>

                        <div class="timeline-icon">
                            {{ $event['icon'] ?? '📌' }}
                        </div>

                        <div class="timeline-content">
                            <div class="card">
                                <div class="card-body py-3">

                                    {{-- VIEW --}}
                                    <div id="event-view-{{ $eventId }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="fw-bold">
                                                {{ $event['title'] }}
                                            </div>

                                            <div class="text-muted small d-flex align-items-center gap-2">
                                                <span>
                                                    {{ \Carbon\Carbon::parse($event['date'])->format('d.m.Y') }}
                                                </span>

                                                @if(!$isSystem)
                                                    <button class="btn btn-sm btn-outline-primary p-1"
                                                            title="Редактировать"
                                                            onclick="toggleEventEdit('{{ $eventId }}')">
                                                        ✏️
                                                    </button>

                                                    <form method="POST"
                                                          action="{{ route('events.destroy', [$person, $event['id']]) }}"
                                                          onsubmit="return confirm('Удалить событие?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger p-1"
                                                                title="Удалить">
                                                            🗑
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>

                                        @if(!empty($event['description']))
                                            <div class="text-muted small mt-1">
                                                {{ $event['description'] }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- EDIT --}}
                                    @if(!$isSystem)
                                        <div id="event-edit-{{ $eventId }}" style="display:none;">
                                            <form method="POST"
                                                  action="{{ route('events.update', [$person, $event['id']]) }}">
                                                @csrf
                                                @method('PATCH')

                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-3">
                                                        <input type="date"
                                                               name="event_date"
                                                               value="{{ $event['date'] }}"
                                                               class="form-control form-control-sm"
                                                               required>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <input type="text"
                                                               name="icon"
                                                               value="{{ $event['icon'] }}"
                                                               class="form-control form-control-sm">
                                                    </div>

                                                    <div class="col-md-7">
                                                        <input type="text"
                                                               name="title"
                                                               value="{{ $event['title'] }}"
                                                               class="form-control form-control-sm"
                                                               required>
                                                    </div>
                                                </div>

                                                <textarea name="description"
                                                          class="form-control form-control-sm mb-2"
                                                          rows="2">{{ $event['description'] }}</textarea>

                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-sm btn-primary">💾 Сохранить</button>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            onclick="toggleEventEdit('{{ $eventId }}')">
                                                        Отмена
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>
@endif

<script>
    function toggleTimeline() {
        const el = document.getElementById('timeline-body');
        if (!el) return;
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function toggleAddEvent() {
        const el = document.getElementById('add-event-form');
        if (!el) return;
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function toggleEventEdit(id) {
        const view = document.getElementById(`event-view-${id}`);
        const edit = document.getElementById(`event-edit-${id}`);
        if (!view || !edit) return;

        const open = edit.style.display === 'none' || edit.style.display === '';
        view.style.display = open ? 'none' : 'block';
        edit.style.display = open ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('timeline-body');
        if (el) el.style.display = 'block';
    });
</script>
