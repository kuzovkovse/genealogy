@extends('layouts.guest')

@section('content')

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required
                   autofocus>

            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Пароль</label>
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required>

            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-check">
                <input type="checkbox" name="remember" class="form-check-input">
                <span class="form-check-label">Запомнить меня</span>
            </label>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">
                Войти
            </button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a href="{{ route('password.request') }}" class="text-muted">
                    Забыли пароль?
                </a>
            </div>
        @endif

        @if (Route::has('register'))
            <div class="text-center mt-2">
                Нет аккаунта?
                <a href="{{ route('register') }}">
                    Зарегистрироваться
                </a>
            </div>
        @endif
    </form>

@endsection
