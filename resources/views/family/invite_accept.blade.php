@extends('layouts.app')

@section('title', 'Приглашение в семью')

@section('content')
    <div class="container" style="max-width: 520px">

        <div class="card shadow-sm">
            <div class="card-body text-center">

                <div class="mb-3" style="font-size:42px">👨‍👩‍👧</div>

                <h4 class="mb-2">Приглашение в семью</h4>

                <p class="text-muted mb-4">
                    Вас пригласили присоединиться к семейному архиву
                </p>

                <div class="border rounded p-3 mb-4 bg-light">
                    <div class="fw-semibold">
                        {{ $invite->family->name ?? 'Семья' }}
                    </div>

                    <div class="text-muted mt-1" style="font-size:14px">
                        Роль:
                        @if($invite->role === 'owner')
                            👑 Владелец
                        @elseif($invite->role === 'editor')
                            ✏️ Редактор
                        @else
                            👀 Наблюдатель
                        @endif
                    </div>
                </div>

                @auth
                    <form method="POST" action="{{ route('family.invites.accept.post', $invite->token) }}">
                        @csrf

                        <button class="btn btn-primary w-100">
                            ✅ Присоединиться к семье
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        Войти и принять приглашение
                    </a>
                @endauth

            </div>
        </div>

    </div>
@endsection
