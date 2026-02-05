@if($grandparentsFather->isNotEmpty() || $grandparentsMother->isNotEmpty())
    <div class="mb-5">

        <h3 class="mb-3">Деды и бабушки</h3>

        {{-- ================= ПО ОТЦУ ================= --}}
        @if($grandparentsFather->isNotEmpty())
            <div class="mb-4 grandparents-block p-3 rounded-4"
                 style="background:#fdf6ec;">

                <div class="fw-semibold mb-3 text-muted">
                    По отцу
                </div>

                {{-- 🔗 линия родства --}}
                <div class="parents-grid kinship-line">
                    @foreach($grandparentsFather as $gp)
                        @php
                            $pb = $gp->birth_date ? \Carbon\Carbon::parse($gp->birth_date) : null;
                            $pd = $gp->death_date ? \Carbon\Carbon::parse($gp->death_date) : null;
                        @endphp

                        <a href="{{ route('people.show', $gp) }}"
                           class="parent-card {{ $gp->death_date ? 'dead' : '' }}">

                            <img class="parent-photo"
                                 src="{{ $gp->photo
                                    ? asset('storage/'.$gp->photo)
                                    : route('avatar', [
                                        'name' => mb_substr($gp->first_name,0,1)
                                            .mb_substr($gp->last_name ?? '',0,1),
                                        'gender' => $gp->gender
                                    ])
                                 }}">

                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge"
                                          style="background:#fef3c7;color:#92400e;">
                                        {{ $gp->gender === 'male' ? 'Дед' : 'Бабушка' }}
                                    </span>

                                    <div class="parent-name">
                                        {{ $gp->last_name }} {{ $gp->first_name }}

                                        @if(
                                            $gp->gender === 'female'
                                            && $gp->birth_last_name
                                            && $gp->birth_last_name !== $gp->last_name
                                        )
                                            <span class="text-muted" style="font-size:13px;">
                                                (урожд. {{ $gp->birth_last_name }})
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="parent-life">
                                    {{ $pb?->year ?? '?' }} — {{ $pd?->year ?? 'н.в.' }}
                                    @if($pd) 🕯 @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ================= ПО МАТЕРИ ================= --}}
        @if($grandparentsMother->isNotEmpty())
            <div class="mb-4 grandparents-block p-3 rounded-4"
                 style="background:#fdf6ec;">

                <div class="fw-semibold mb-3 text-muted">
                    По матери
                </div>

                {{-- 🔗 линия родства --}}
                <div class="parents-grid kinship-line">
                    @foreach($grandparentsMother as $gp)
                        @php
                            $pb = $gp->birth_date ? \Carbon\Carbon::parse($gp->birth_date) : null;
                            $pd = $gp->death_date ? \Carbon\Carbon::parse($gp->death_date) : null;
                        @endphp

                        <a href="{{ route('people.show', $gp) }}"
                           class="parent-card {{ $gp->death_date ? 'dead' : '' }}">

                            <img class="parent-photo"
                                 src="{{ $gp->photo
                                    ? asset('storage/'.$gp->photo)
                                    : route('avatar', [
                                        'name' => mb_substr($gp->first_name,0,1)
                                            .mb_substr($gp->last_name ?? '',0,1),
                                        'gender' => $gp->gender
                                    ])
                                 }}">

                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge"
                                          style="background:#fef3c7;color:#92400e;">
                                        {{ $gp->gender === 'male' ? 'Дед' : 'Бабушка' }}
                                    </span>

                                    <div class="parent-name">
                                        {{ $gp->last_name }} {{ $gp->first_name }}

                                        @if(
                                            $gp->gender === 'female'
                                            && $gp->birth_last_name
                                            && $gp->birth_last_name !== $gp->last_name
                                        )
                                            <span class="text-muted" style="font-size:13px;">
                                                (урожд. {{ $gp->birth_last_name }})
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="parent-life">
                                    {{ $pb?->year ?? '?' }} — {{ $pd?->year ?? 'н.в.' }}
                                    @if($pd) 🕯 @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endif
