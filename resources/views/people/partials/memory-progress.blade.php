@php
    $percent = $progress['score'] ?? 0;
@endphp

{{-- 🧩 ПРОГРЕСС ПАМЯТИ --}}
<div class="card mb-4">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-semibold">
                🧠 Прогресс памяти
            </div>
            <div class="text-muted small">
                {{ $percent }}%
            </div>
        </div>

        {{-- ПРОГРЕСС-БАР --}}
        <div class="progress mb-2" style="height:8px;">
            <div class="progress-bar bg-success"
                 role="progressbar"
                 style="width: {{ $percent }}%;"
                 aria-valuenow="{{ $percent }}"
                 aria-valuemin="0"
                 aria-valuemax="100">
            </div>
        </div>

        {{-- ТЕКСТ --}}
        @if($percent === 100)
            <div class="text-success small">
                История сохранена полностью 🌿
            </div>
        @else
            <div class="text-muted small">
                История заполнена на {{ $percent }}%.
                Осталось совсем немного.
            </div>
        @endif

    </div>
</div>
