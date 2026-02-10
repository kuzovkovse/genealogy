@props(['log'])

<div class="relative group">

    {{-- точка --}}
    <div class="absolute -left-[9px] top-2 w-3 h-3 rounded-full
        {{ $log->isSilent() ? 'bg-gray-300' : 'bg-blue-500' }}">
    </div>

    {{-- карточка --}}
    <div class="bg-white rounded-xl border px-4 py-3
        transition hover:shadow-md hover:border-gray-300
        {{ $log->isSilent() ? 'border-gray-100' : 'border-gray-200' }}">

        <div class="flex gap-3">

            @if ($log->icon())
                <div class="text-xl mt-0.5">
                    {{ $log->icon() }}
                </div>
            @endif

            <div class="flex-1">

                {{-- заголовок --}}
                <div class="flex items-start gap-2">
                    <div class="{{ $log->isSilent() ? 'text-gray-500' : 'font-medium' }}">
                        {{ $log->title() }}
                    </div>

                    @if ($log->isNewForUser())
                        <span class="text-blue-500 text-xs mt-1">●</span>
                    @endif
                </div>

                {{-- изменения --}}
                @if ($log->changesText())
                    <div class="text-sm text-gray-500 mt-1 leading-relaxed">
                        {!! $log->changesText() !!}
                    </div>
                @endif

                {{-- ссылка --}}
                @if ($log->personId())
                    <a href="{{ url('/people/' . $log->personId()) }}"
                       class="inline-block text-xs text-blue-600 hover:underline mt-2">
                        перейти к человеку →
                    </a>
                @endif

                {{-- время --}}
                <div class="text-xs text-gray-400 mt-2">
                    {{ $log->created_at->diffForHumans() }}
                    · {{ $log->created_at->format('d.m.Y H:i') }}
                </div>

            </div>
        </div>
    </div>
</div>
