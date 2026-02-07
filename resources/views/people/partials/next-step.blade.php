@if(!empty($step))
    <div class="mt-3 mb-2 text-muted small">

        <span class="me-1">{{ $step['icon'] }}</span>
        {{ $step['text'] }}

        @if(!empty($step['action']))
            <span class="ms-2 text-primary"
                  role="button"
                  style="cursor:pointer"
                  onclick="{{ $step['action']['js'] }}">
    {{ $step['action']['label'] }}
</span>
        @endif

    </div>
@endif
