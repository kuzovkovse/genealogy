@php
    $percent = $progress['score'] ?? 0;
    $missing = $progress['missing'] ?? [];

    $actions = [
        'biography' => [
            'label'  => 'заполнить историю жизни',
            'scroll' => '#biography-block',
        ],
        'photos' => [
            'label'  => 'добавить фотографии',
            'action' => 'open-gallery-form',
        ],
        'parents' => [
            'label'  => 'указать родителей',
            'scroll' => '#parents-block',
        ],
        'partner' => [
            'label'  => 'добавить партнёра',
            'scroll' => '#marriages-block',
        ],
    ];
@endphp

<div class="card mb-4">
    <div class="card-body">

        {{-- Заголовок --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">🧠 Прогресс памяти</div>
            <div class="text-muted small">{{ $percent }}%</div>
        </div>

        {{-- Прогресс --}}
        <div class="progress mb-2" style="height:8px;">
            <div class="progress-bar bg-success"
                 role="progressbar"
                 style="width: {{ $percent }}%;"
                 aria-valuenow="{{ $percent }}"
                 aria-valuemin="0"
                 aria-valuemax="100"></div>
        </div>

        @if($percent === 100)
            <div class="text-success small">
                История сохранена полностью 🌿
            </div>
        @else
            <div class="text-muted small mb-2">
                История заполнена на {{ $percent }}%.
                Можно дополнить, если знаете:
            </div>

            <ul class="small mb-0 ps-3">
                @foreach(array_slice($missing, 0, 3) as $key)
                    @if(isset($actions[$key]))
                        <li>
                            <button type="button"
                                    class="btn btn-link p-0 memory-progress-link"
                                    @if(isset($actions[$key]['scroll']))
                                        data-scroll="{{ $actions[$key]['scroll'] }}"
                                    @endif
                                    @if(isset($actions[$key]['action']))
                                        data-action="{{ $actions[$key]['action'] }}"
                                @endif>
                                {{ $actions[$key]['label'] }}
                            </button>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

    </div>
</div>
