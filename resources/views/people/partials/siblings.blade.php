@if($siblings->isNotEmpty())
    @php
        $siblingsSorted = $siblings
            ->sortBy(fn($s) => $s->birth_date ?? '9999-12-31')
            ->values();

        $count = $siblingsSorted->count();
    @endphp

    <div class="mb-5 grandparents-block">
        <h3 class="mb-3">Братья и сёстры</h3>

        <div class="parents-grid">
            @foreach($siblingsSorted as $i => $sibling)
                @php
                    // порядок
                    if ($count < 2) {
                        $order = null;
                    } elseif ($i === 0) {
                        $order = 'Старший';
                    } elseif ($i === $count - 1) {
                        $order = 'Младший';
                    } else {
                        $order = 'Средний';
                    }

                    $role = $sibling->gender === 'male' ? 'Брат' : 'Сестра';

                    $birthYear = $sibling->birth_date
                        ? \Illuminate\Support\Str::of($sibling->birth_date)->substr(0, 4)
                        : '?';

                    $deathYear = $sibling->death_date
                        ? \Illuminate\Support\Str::of($sibling->death_date)->substr(0, 4)
                        : 'н.в.';
                @endphp

                <a href="{{ route('people.show', $sibling) }}"
                   class="parent-card {{ $sibling->death_date ? 'dead' : '' }}">

                    <img class="parent-photo"
                         src="{{ $sibling->photo
                            ? asset('storage/'.$sibling->photo)
                            : route('avatar', [
                                'name' => mb_substr($sibling->first_name,0,1).mb_substr($sibling->last_name ?? '',0,1),
                                'gender' => $sibling->gender
                            ])
                         }}">

                    <div>
                        <div class="parent-name">
                            {{ $sibling->first_name }}
                            <span class="gp-badge">{{ $role }}</span>
                        </div>

                        <div class="parent-life">
                            {{ $birthYear }} — {{ $deathYear }}
                            @if($sibling->death_date) 🕯 @endif
                        </div>

                        @if($order)
                            <div class="text-muted small">
                                {{ $order }}
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
