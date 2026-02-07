@php
    $lastCandles = $person->memorialCandles()
        ->latest('lit_at')
        ->take(5)
        ->get();

    $activeCandlesCount = $person->activeCandlesCount();
@endphp

<div class="memorial-card mt-4 text-center py-3">

    {{-- ИКОНКА --}}
    <div style="font-size:36px;">🕯</div>

    {{-- СЧЁТЧИК --}}
    <div class="fw-semibold mt-1">
        Зажжено свечей: {{ $activeCandlesCount }}
        <small class="text-muted">за последние 24 часа</small>
    </div>

    {{-- ПОДПИСЬ --}}
    <div class="text-muted small mt-1">
        Каждая свеча — знак памяти
    </div>

    {{-- КНОПКА --}}
    <form method="POST"
          action="{{ route('people.memorial.candle', $person) }}"
          class="mt-2">
        @csrf

        @guest
            <input type="text"
                   name="visitor_name"
                   class="form-control form-control-sm mb-2"
                   placeholder="Ваше имя (необязательно)">
        @endguest

        <button type="submit"
                class="btn btn-outline-warning btn-sm">
            🕯 Зажечь свечу
        </button>
    </form>

    {{-- ИСТОРИЯ (СКРЫТАЯ) --}}
    @if($lastCandles->count())
        <div class="mt-3 text-start small">

            <span class="text-muted"
                  role="button"
                  style="cursor:pointer"
                  onclick="toggleCandlesHistory()">
    Свечи памяти ▾
</span>

            <div id="candles-history"
                 class="mt-2"
                 style="display:none;">
                @foreach($lastCandles as $candle)
                    🕯 {{ $candle->visitor_name ?? 'Аноним' }}
                    · {{ optional($candle->lit_at)->locale('ru')->diffForHumans() }}<br>
                @endforeach

                <div class="text-muted mt-1" style="font-size:12px;">
                    Каждая свеча — чьё-то воспоминание
                </div>
            </div>
        </div>
    @endif

</div>

<script>
    function toggleCandlesHistory() {
        const el = document.getElementById('candles-history');
        if (!el) return;

        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
</script>
