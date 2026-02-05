@if($siblings->isNotEmpty())
    @php
        $siblingsSorted = $siblings
            ->sortBy(fn($dto) => $dto->person->birth_date ?? '9999-12-31')
            ->values();

        $count = $siblingsSorted->count();
    @endphp

    <div class="mb-5 grandparents-block">
        <h3 class="mb-3">Братья и сёстры</h3>

        <div class="parents-grid">
            @foreach($siblingsSorted as $i => $dto)
                @php
                    $person = $dto->person;

                    // порядок рождения
                    if ($count < 2) {
                        $order = null;
                    } elseif ($i === 0) {
                        $order = 'Старший';
                    } elseif ($i === $count - 1) {
                        $order = 'Младший';
                    } else {
                        $order = 'Средний';
                    }

                    $role = $person->gender === 'male' ? 'Брат' : 'Сестра';

                    $birthYear = $person->birth_date
                        ? \Illuminate\Support\Str::of($person->birth_date)->substr(0, 4)
                        : '?';

                    $deathYear = $person->death_date
                        ? \Illuminate\Support\Str::of($person->death_date)->substr(0, 4)
                        : 'н.в.';
                @endphp

                <a href="{{ route('people.show', $person) }}"
                   class="parent-card {{ $person->death_date ? 'dead' : '' }}">

                    <img class="parent-photo"
                         src="{{ $person->photo
                            ? asset('storage/'.$person->photo)
                            : route('avatar', [
                                'name' => mb_substr($person->first_name,0,1).mb_substr($person->last_name ?? '',0,1),
                                'gender' => $person->gender
                            ])
                         }}">

                    <div>
                        <div class="parent-name">
                            {{ $person->first_name }}

                            <span class="gp-badge">
                                {{ $role }}
                            </span>

                            {{-- подпись родства --}}
                            @if($dto->label())
                                <span class="text-muted small">
                                    ({{ $dto->label() }})
                                </span>
                            @endif
                        </div>

                        <div class="parent-life">
                            {{ $birthYear }} — {{ $deathYear }}
                            @if($person->death_date) 🕯 @endif
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
