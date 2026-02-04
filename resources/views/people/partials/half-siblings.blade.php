@if($halfSiblingsFather->isNotEmpty() || $halfSiblingsMother->isNotEmpty())
    <div class="mb-5">
        <h3 class="mb-3">
            Сводные братья и сёстры
            <span class="text-muted" style="font-size:14px;">
                (общий только один родитель)
            </span>
        </h3>

        {{-- ПО ОТЦУ --}}
        @if($halfSiblingsFather->isNotEmpty())
            <div class="mb-4">
                <div class="fw-semibold mb-2 text-muted">По отцу</div>

                <div class="parents-grid">
                    @foreach($halfSiblingsFather as $sibling)
                        @php
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
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ПО МАТЕРИ --}}
        @if($halfSiblingsMother->isNotEmpty())
            <div>
                <div class="fw-semibold mb-2 text-muted">По матери</div>

                <div class="parents-grid">
                    @foreach($halfSiblingsMother as $sibling)
                        @php
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
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
