@php
    $lastCandles = $person->memorialCandles()
        ->latest('lit_at')
        ->take(5)
        ->get();

    $activeCandlesCount = $person->activeCandlesCount();

    $isDead = !is_null($person->death_date);
    $canLightCandle = $isDead && $activeCandlesCount < 3;
@endphp

<style>
    .memorial-card {
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(0,0,0,.05);
    }

    .candle-icon {
        font-size: 36px;
        transition: transform .2s ease;
    }

    .candle-icon.lit {
        animation: flame 1.2s infinite ease-in-out;
        transform-origin: center bottom;
    }

    @keyframes flame {
        0%   { transform: scale(1) rotate(-1deg); }
        25%  { transform: scale(1.05) rotate(1deg); }
        50%  { transform: scale(1.1) rotate(-1deg); }
        75%  { transform: scale(1.05) rotate(1deg); }
        100% { transform: scale(1) rotate(-1deg); }
    }

    button.loading {
        opacity: .6;
        pointer-events: none;
    }
</style>

<div class="memorial-card mt-4 text-center py-3">

    {{-- 🔥 ИКОНКА --}}
    <div id="candle-icon" class="candle-icon">🕯</div>

    {{-- 🔢 СЧЁТЧИК --}}
    <div id="candle-counter" class="fw-semibold mt-1">
        Зажжено свечей: {{ $activeCandlesCount }}
        <small class="text-muted">за последние 24 часа</small>
    </div>

    {{-- 📝 ПОДПИСЬ --}}
    <div class="text-muted small mt-1">
        Каждая свеча — знак памяти
    </div>

    {{-- ⚠️ ПРИЧИНЫ БЛОКИРОВКИ --}}
    @unless($isDead)
        <div class="text-muted small mt-2">
            🪦 Свечу можно зажечь только для умершего человека
        </div>
    @elseif($activeCandlesCount >= 3)
        <div class="text-muted small mt-2">
            ⏳ Сейчас уже горит несколько свечей. Попробуйте позже
        </div>
    @endunless

    {{-- 🔘 КНОПКА --}}
    <div class="mt-2">
        <button id="light-candle-btn"
                class="btn btn-outline-warning btn-sm"
                @unless($canLightCandle) disabled @endunless>
            🕯 Зажечь свечу
        </button>

        <div id="candle-error"
             class="text-danger small mt-2 d-none"></div>
    </div>

    {{-- 📜 ИСТОРИЯ --}}
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
    (() => {
        const btn = document.getElementById('light-candle-btn');
        const icon = document.getElementById('candle-icon');
        const counter = document.getElementById('candle-counter');
        const errorBox = document.getElementById('candle-error');

        if (!btn) return;

        let optimisticCount = {{ $activeCandlesCount }};

        // если уже есть свечи — пламя должно гореть
        if (optimisticCount > 0) {
            icon.classList.add('lit');
        }

        btn.addEventListener('click', async () => {
            errorBox.classList.add('d-none');
            btn.classList.add('loading');

            // 🔥 optimistic increment
            optimisticCount++;
            counter.innerHTML = `Зажжено свечей: ${optimisticCount}
            <small class="text-muted">за последние 24 часа</small>`;
            icon.classList.add('lit');

            try {
                const response = await fetch("{{ route('people.memorial.candle', $person) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                // ❗ ВАЖНО: если сервер ответил, но запретил — это НЕ network error
                if (!response.ok) {
                    let message = 'Действие сейчас недоступно';

                    try {
                        const data = await response.json();
                        message = data.message || message;
                    } catch (_) {}

                    // 👉 отменяем optimistic increment
                    optimisticCount--;
                    counter.innerHTML = `Зажжено свечей: ${optimisticCount}
                    <small class="text-muted">за последние 24 часа</small>`;

                    // ❗ НЕ убираем пламя, если есть свечи
                    if (optimisticCount === 0) {
                        icon.classList.remove('lit');
                    }

                    errorBox.textContent = message;
                    errorBox.classList.remove('d-none');
                    return;
                }

                const data = await response.json();

                // сервер подтвердил актуальное число
                optimisticCount = data.active_count;
                counter.innerHTML = `Зажжено свечей: ${optimisticCount}
                <small class="text-muted">за последние 24 часа</small>`;

                if (optimisticCount > 0) {
                    icon.classList.add('lit');
                }

            } catch (e) {
                // ❌ ТОЛЬКО реальная ошибка соединения
                optimisticCount--;
                counter.innerHTML = `Зажжено свечей: ${optimisticCount}
                <small class="text-muted">за последние 24 часа</small>`;

                if (optimisticCount === 0) {
                    icon.classList.remove('lit');
                }

                errorBox.textContent = 'Ошибка соединения';
                errorBox.classList.remove('d-none');
            } finally {
                btn.classList.remove('loading');
            }
        });
    })();
</script>

