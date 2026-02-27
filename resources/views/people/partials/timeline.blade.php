@if($timeline->isNotEmpty())

    <style>
        /* ===================================
           HISTORICAL TIMELINE STYLE
        =================================== */

        .timeline-wrapper {
            position: relative;
            padding-left: 28px;
        }

        .timeline-wrapper::before {
            content: "";
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #d4af37, #e5e7eb);
        }

        .timeline-event {
            position: relative;
            margin-bottom: 14px;
        }

        .timeline-dot {
            position: absolute;
            left: -18px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #d4af37;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #f3f4f6;
        }

        .timeline-icon {
            font-size: 14px;
            margin-right: 6px;
        }

        .timeline-title {
            font-weight: 600;
            font-size: 14px;
            color: #111827;
        }

        .timeline-date {
            font-size: 12px;
            color: #6b7280;
            margin-left: 6px;
        }

        .timeline-description {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .timeline-narrative {
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
            margin: 4px 0 10px 0;
        }

        .timeline-actions button {
            border: none;
            background: none;
            font-size: 13px;
            padding: 2px 4px;
            color: #9ca3af;
        }

        .timeline-actions button:hover {
            color: #111827;
        }

        /* ===============================
   TIMELINE ANIMATION
=============================== */

        .timeline-event {
            opacity: 0;
            transform: translateY(6px);
            animation: fadeUp 0.5s ease forwards;
        }

        .timeline-event:nth-child(1) { animation-delay: 0.05s; }
        .timeline-event:nth-child(2) { animation-delay: 0.1s; }
        .timeline-event:nth-child(3) { animation-delay: 0.15s; }
        .timeline-event:nth-child(4) { animation-delay: 0.2s; }
        .timeline-event:nth-child(5) { animation-delay: 0.25s; }
        .timeline-event:nth-child(6) { animation-delay: 0.3s; }
        .timeline-event:nth-child(7) { animation-delay: 0.35s; }
        .timeline-event:nth-child(8) { animation-delay: 0.4s; }
        .timeline-event:nth-child(9) { animation-delay: 0.45s; }
        .timeline-event:nth-child(10){ animation-delay: 0.5s; }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Narrative тоже анимируем, но мягче */
        .timeline-narrative {
            opacity: 0;
            animation: fadeNarrative 0.6s ease forwards;
            animation-delay: 0.2s;
        }

        @keyframes fadeNarrative {
            to {
                opacity: 1;
            }
        }

    </style>

    <div class="timeline-card mb-4">

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
            <div class="timeline-wrapper">

                @foreach($timeline as $event)

                    @php
                        $isSystem = $event['is_system'] ?? false;
                        $model = $event['model'] ?? null;
                        $eventId = $model?->id;
                    @endphp

                    {{-- Narrative (прошло N лет и т.п.) --}}
                    @if(($event['type'] ?? null) === 'narrative')
                        <div class="timeline-narrative">
                            {{ str_replace("\n", ' · ', $event['text']) }}
                        </div>
                        @continue
                    @endif

                    <div class="timeline-event">

                        <div class="timeline-dot"></div>

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <div class="timeline-title">
                                <span class="timeline-icon">
                                    {{ $event['icon'] ?? '📌' }}
                                </span>

                                    {{ $event['title'] }}

                                    <span class="timeline-date">
                                    — {{ \Carbon\Carbon::parse($event['event_date'])->format('d.m.Y') }}
                                </span>
                                </div>

                                @if(!empty($event['description']))
                                    <div class="timeline-description">
                                        {{ $event['description'] }}
                                    </div>
                                @endif
                            </div>

                            {{-- ACTIONS --}}
                            @if(!$isSystem && $model)
                                <div class="timeline-actions d-flex gap-1">

                                    <button title="Редактировать"
                                            onclick="toggleEventEdit('{{ $eventId }}')">
                                        ✏
                                    </button>

                                    <form method="POST"
                                          action="{{ route('events.destroy', [$person, $eventId]) }}"
                                          onsubmit="return confirm('Удалить событие?')">
                                        @csrf
                                        @method('DELETE')
                                        <button title="Удалить">
                                            🗑
                                        </button>
                                    </form>

                                </div>
                            @endif

                        </div>

                        {{-- EDIT --}}
                        @if(!$isSystem && $model)
                            <div id="event-edit-{{ $eventId }}"
                                 style="display:none; margin-top:6px;">
                                <form method="POST"
                                      action="{{ route('events.update', [$person, $eventId]) }}">
                                    @csrf
                                    @method('PATCH')

                                    <div class="row g-2 mb-2">
                                        <div class="col-md-3">
                                            <input type="date"
                                                   name="event_date"
                                                   value="{{ $event['event_date'] }}"
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
                                        <button class="btn btn-sm btn-primary">💾</button>
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

                @endforeach

            </div>
        </div>

        @include('people.partials.next-step', [
            'step' => $nextSteps['timeline'] ?? null
        ])

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
        const el = document.getElementById('event-edit-' + id);
        if (!el) return;
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
</script>
