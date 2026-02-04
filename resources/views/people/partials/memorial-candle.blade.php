@php
    $lastCandles = $person->memorialCandles()
        ->latest('lit_at')
        ->take(5)
        ->get();

    $activeCandlesCount = $person->activeCandlesCount();
@endphp

<div class="memorial-card mt-4 text-center">

    <div style="font-size:42px;">🕯</div>

    <div class="fw-semibold mt-2">
        Зажжено свечей: {{ $activeCandlesCount }}
        <small class="text-muted">за последние 24 часа</small>
    </div>

    <div class="text-muted small mt-1">
        Каждая свеча — знак памяти
    </div>

    <form method="POST"
          action="{{ route('people.memorial.candle', $person) }}"
          class="mt-3">
        @csrf

        @guest
            <input type="text"
                   name="visitor_name"
                   class="form-control mb-2"
                   placeholder="Ваше имя (необязательно)">
        @endguest

        <button type="submit" class="btn btn-outline-warning">
            🕯 Зажечь свечу
        </button>
    </form>

    @if($lastCandles->count())
        <div class="mt-4 text-start small">
            <div class="fw-semibold mb-2">Последние свечи:</div>

            @foreach($lastCandles as $candle)
                🕯 {{ $candle->visitor_name ?? 'Аноним' }}
                · {{ optional($candle->lit_at)->diffForHumans() }}<br>
            @endforeach
        </div>
    @endif
</div>
