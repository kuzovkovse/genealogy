@php
    $greatGrandparents = $kinship->ancestors
        ->where('depth', 3)
        ->groupBy('line');
@endphp

@if($greatGrandparents->isNotEmpty())
    <div class="mb-5 grandparents-block kinship-line">
        <h3 class="mb-3">Прадеды и прабабушки</h3>

        <div class="parents-grid">
            @foreach($greatGrandparents as $line => $items)
                @foreach($items as $item)
                    @php
                        $person = $item['person'];

                        $birthYear = $person->birth_date
                            ? \Illuminate\Support\Str::of($person->birth_date)->substr(0, 4)
                            : '?';

                        $deathYear = $person->death_date
                            ? \Illuminate\Support\Str::of($person->death_date)->substr(0, 4)
                            : 'н.в.';

                        $lineLabel = $line === 'paternal'
                            ? 'по отцу'
                            : 'по матери';
                    @endphp

                    <a href="{{ route('people.show', $person) }}"
                       class="parent-card {{ $person->death_date ? 'dead' : '' }}">

                        <img class="parent-photo"
                             src="{{ $person->photo
                                ? asset('storage/'.$person->photo)
                                : route('avatar', [
                                    'name' => mb_substr($person->first_name,0,1)
                                        .mb_substr($person->last_name ?? '',0,1),
                                    'gender' => $person->gender
                                ])
                             }}">

                        <div>
                            <div class="parent-name">
                                {{ $person->first_name }}

                                <span class="text-muted small">
                                    ({{ $lineLabel }})
                                </span>
                            </div>

                            <div class="parent-life">
                                {{ $birthYear }} — {{ $deathYear }}
                                @if($person->death_date) 🕯 @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
@endif
