@php
    use App\Models\FamilyAuditLog;

    $family = app()->bound('activeFamily') ? app('activeFamily') : null;

    $notifications = $family
        ? FamilyAuditLog::where('family_id', $family->id)
            ->latest()
            ->limit(6)
            ->get()
        : collect();

    $unreadCount = $notifications->filter(fn ($log) => $log->isNewForUser())->count();
@endphp

<div class="dropdown">

    {{-- 🔔 КНОПКА --}}
    <button
        class="btn position-relative p-0 border-0 bg-transparent"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        title="Уведомления семьи"
    >
        <span style="font-size: 18px">🔔</span>

        @if ($unreadCount > 0)
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="font-size: 10px"
            >
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- 📜 DROPDOWN --}}
    <div
        class="dropdown-menu dropdown-menu-end shadow p-0"
        style="width: 550px; max-height: 420px; overflow-y: auto;"
    >

        {{-- HEADER --}}
        <div class="px-3 py-2 border-bottom bg-light fw-semibold">
            Уведомления семьи
        </div>

        @forelse ($notifications as $log)
            <a
                href="{{ $log->personId() ? url('/people/' . $log->personId()) : '#' }}"
                class="dropdown-item px-3 py-2 d-flex gap-2 align-items-start
                       {{ $log->isNewForUser() ? 'bg-primary bg-opacity-10' : '' }}"
            >

                {{-- ICON --}}
                <div style="width: 20px">
                    {{ $log->icon() ?? '•' }}
                </div>

                {{-- TEXT --}}
                <div class="flex-grow-1">
                    <div class="small text-dark">
                        {{ $log->title() }}
                    </div>

                    <div class="text-muted" style="font-size: 11px">
                        {{ $log->created_at->diffForHumans() }}
                    </div>
                </div>

            </a>
        @empty
            <div class="px-3 py-4 text-center text-muted small">
                Пока нет событий
            </div>
        @endforelse

        {{-- FOOTER --}}
        <div class="border-top text-center">
            <a
                href="{{ route('family.history') }}"
                class="dropdown-item small text-primary fw-medium"
            >
                Вся история →
            </a>
        </div>
    </div>
</div>
