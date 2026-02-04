@if($person->death_date)
    @php
        $hasMemorial =
            $person->burial_cemetery
            || $person->burial_city
            || $person->burial_place
            || $person->burial_description
            || $person->burial_lat
            || $person->burial_lng;
    @endphp

    <div class="mb-5 memorial-place-block" id="memorial-place">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">🕯 Место памяти</h3>

            @can('update', $person)
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    onclick="toggleMemorialEdit()"
                >
                    {{ $hasMemorial ? '✏️ Редактировать' : '➕ Добавить' }}
                </button>
            @endcan
        </div>

        {{-- VIEW --}}
        <div id="memorial-view" class="memorial-card">
            @if($hasMemorial)

                <div class="memorial-grid">

                    @if($person->burial_cemetery)
                        <div>
                            <div class="memorial-label">Кладбище</div>
                            <div class="memorial-value">{{ $person->burial_cemetery }}</div>
                        </div>
                    @endif

                    @if($person->burial_city)
                        <div>
                            <div class="memorial-label">Город</div>
                            <div class="memorial-value">{{ $person->burial_city }}</div>
                        </div>
                    @endif

                    @if($person->burial_place)
                        <div>
                            <div class="memorial-label">Участок / место</div>
                            <div class="memorial-value">{{ $person->burial_place }}</div>
                        </div>
                    @endif

                </div>

                @if($person->burial_description)
                    <div class="mt-4">
                        <div class="memorial-label mb-1">Как найти</div>
                        <div class="memorial-description">
                            {{ $person->burial_description }}
                        </div>
                    </div>
                @endif

                @if($person->memorialPhotos->count())
                    <div class="row g-3 mt-3">
                        @foreach($person->memorialPhotos as $photo)
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="{{ asset('storage/'.$photo->image_path) }}"
                                   class="glightbox"
                                   data-gallery="memorial">

                                    <img src="{{ asset('storage/'.$photo->image_path) }}"
                                         class="img-fluid rounded"
                                         style="aspect-ratio:1/1; object-fit:cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted small mt-3">
                        Фотографии места памяти ещё не добавлены
                    </div>
                @endif

                @can('update', $person)
                    @include('people.partials.memorial-photos-form')
                @endcan

                @if($person->burial_lat && $person->burial_lng)
                    <div class="mt-3">
                        <a class="btn btn-sm btn-outline-secondary"
                           target="_blank"
                           href="https://maps.google.com/?q={{ $person->burial_lat }},{{ $person->burial_lng }}">
                            🗺 Открыть на карте
                        </a>
                    </div>
                @endif

            @else
                <div class="memorial-empty">
                    <div class="mb-1">Информация о месте захоронения пока не добавлена</div>
                    <div class="text-muted small">
                        Часто такие сведения теряются со временем — вы можете помочь сохранить память
                    </div>
                </div>
            @endif
        </div>
        @include('people.partials.memorial-candle')
        {{-- EDIT (DOM ВСЕГДА ЕСТЬ) --}}
        <div id="memorial-edit" class="mt-4" style="display:none;">
            @can('update', $person)
                @include('people.partials.memorial-place-form')
            @endcan
        </div>

    </div>

@endif
