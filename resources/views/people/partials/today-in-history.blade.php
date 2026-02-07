@if($todayInHistory)
    <div class="card mb-4">
        <div class="card-body text-center">

            <div style="font-size:24px; margin-bottom:6px;">
                {{ $todayInHistory['icon'] }}
            </div>

            <div class="fw-semibold">
                {{ $todayInHistory['title'] }}
            </div>

            <div class="text-muted small mb-2">
                {{ $todayInHistory['date'] }}
            </div>

            <div class="fst-italic">
                {{ $todayInHistory['text'] }}
            </div>

        </div>
    </div>
@endif
