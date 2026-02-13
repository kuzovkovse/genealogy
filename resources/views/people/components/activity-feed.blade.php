@props([
    'logs',
    'showDateGroups' => true,
])

<div class="space-y-6">

    @forelse ($logs as $day => $items)

        @if ($showDateGroups)
            <details class="group" open>
                <summary class="cursor-pointer flex items-center gap-2 text-sm text-gray-600 mb-3">
                    <span class="font-medium">
                        {{ \Carbon\Carbon::parse($day)->isoFormat('D MMMM YYYY') }}
                    </span>
                    <span class="text-gray-400">
                        ({{ count($items) }})
                    </span>
                    <span class="ml-auto text-gray-400 group-open:rotate-180 transition">
                        ▾
                    </span>
                </summary>
                @endif

                <div class="relative pl-6 border-l border-gray-200 space-y-4">

                    @foreach ($items as $log)
                        @include('people.partials.activity-item', ['log' => $log])
                    @endforeach

                </div>

                @if ($showDateGroups)
            </details>
        @endif

    @empty
        @include('people.partials.empty-state', ['text' => 'Пока нет событий'])
    @endforelse

</div>
