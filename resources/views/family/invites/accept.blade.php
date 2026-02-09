@extends('layouts.app')

@section('title', 'Приглашение в семью')

@section('content')

    <div class="container" style="max-width: 720px">

        <div class="card mt-4">
            <div class="card-body">

                <h1 class="mb-3">
                    👨‍👩‍👧 Приглашение в семью
                </h1>

                {{-- Ошибки --}}
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Успех --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Основной блок --}}
                @isset($invite)

                    <p class="mb-3">
                        Вас приглашают присоединиться к семейному архиву:
                    </p>

                    <div class="mb-3">
                        <strong>Семья:</strong><br>
                        {{ $invite->family->name ?? 'Семейное древо' }}
                    </div>

                    <div class="mb-4">
                        <strong>Роль:</strong><br>

                        @switch($invite->role)
                            @case('owner')
                                👑 Владелец
                                @break
                            @case('editor')
                                ✏️ Редактор
                                @break
                            @default
                                👁 Наблюдатель
                        @endswitch
                    </div>

                    {{-- Если пользователь не авторизован --}}
                    @guest
                        <div class="alert alert-info">
                            Чтобы принять приглашение, необходимо войти в систему.
                        </div>

                        <a href="{{ route('login') }}" class="btn btn-primary">
                            🔐 Войти
                        </a>

                        <a href="{{ route('register') }}" class="btn btn-outline-secondary ms-2">
                            🆕 Зарегистрироваться
                        </a>
                    @endguest

                    {{-- Если пользователь авторизован --}}
                    @auth
                        <form method="POST" action="{{ route('family.invites.accept', $invite->token) }}">
                            @csrf

                            <button type="submit" class="btn btn-success">
                                ✅ Принять приглашение
                            </button>
                        </form>
                    @endauth

                @else
                    <div class="alert alert-warning">
                        Приглашение не найдено или уже недействительно.
                    </div>
                @endisset

            </div>
        </div>
    </div>

@endsection
