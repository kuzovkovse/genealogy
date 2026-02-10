@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-semibold flex items-center gap-2">
                🕊️ История семьи
            </h1>
            <p class="text-gray-500 mt-1 text-sm">
                Важные и фоновые изменения в жизни семьи
            </p>
        </div>

        {{-- Timeline --}}
        @foreach ($logsByDay as $day => $logs)
            <details class="mb-8 group" {{ $loop->first ? 'open' : '' }}>
                <summary
                    class="cursor-pointer select-none flex items-center gap-2 text-sm text-gray-600 mb-4 hover:text-gray-900 transition">
                <span class="font-medium">
                    {{ \Carbon\Carbon::parse($day)->isoFormat('D MMMM YYYY') }}
                </span>
                    <span class="text-gray-400">
                    ({{ $logs->count() }})
                </span>
                    <span class="ml-auto text-gray-400 group-open:rotate-180 transition">
                    ▾
                </span>
                </summary>

                <div class="relative pl-6 border-l border-gray-200 space-y-4">

                    @foreach ($logs as $log)
                        <div class="relative group">

                            {{-- Dot --}}
                            <div class="absolute -left-[9px] top-2 w-3 h-3 rounded-full
                            {{ $log->isSilent() ? 'bg-gray-300' : 'bg-blue-500' }}">
                            </div>

                            {{-- Card --}}
                            <div class="
                            rounded-lg px-4 py-3 transition
                            {{ $log->isSilent()
                                ? 'bg-gray-50 border border-gray-100'
                                : 'bg-white border border-gray-200 hover:bg-gray-50 shadow-sm'
                            }}
                        ">

                                <div class="flex gap-3">

                                    {{-- Icon --}}
                                    @if ($log->icon())
                                        <div class="text-lg leading-none mt-0.5">
                                            {{ $log->icon() }}
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">

                                        {{-- Title --}}
                                        <div class="flex items-start gap-2">
                                            <div class="{{ $log->isSilent() ? 'text-gray-500' : 'font-medium' }}">
                                                {!! $log->title() !!}
                                            </div>

                                            @if ($log->isNewForUser())
                                                <span class="text-blue-500 text-xs mt-1">●</span>
                                            @endif
                                        </div>

                                        {{-- Changes --}}
                                        @if ($log->changesText())
                                            <div class="text-sm text-gray-500 mt-1 leading-snug">
                                                {!! $log->changesText() !!}
                                            </div>
                                        @endif

                                        {{-- Link --}}
                                        @if ($log->personId())
                                            <a href="{{ url('/people/' . $log->personId()) }}"
                                               class="inline-block text-xs text-blue-600 hover:underline mt-2">
                                                перейти к человеку →
                                            </a>
                                        @endif

                                        {{-- Time --}}
                                        <div class="text-xs text-gray-400 mt-2">
                                            {{ $log->created_at->diffForHumans() }}
                                            · {{ $log->created_at->format('d.m.Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </details>
        @endforeach

        {{-- Back --}}
        <div class="mt-10">
            <a href="{{ url('/people') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Вернуться к людям
            </a>
        </div>

    </div>
@endsection
