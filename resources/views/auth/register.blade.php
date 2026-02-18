@extends('layouts.guest')

@section('content')

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Имя</label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required autofocus>

            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required>

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
            <label class="form-label">Подтверждение пароля</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control"
                   required>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">
                Создать аккаунт
            </button>
        </div>

        <div class="text-center mt-3">
            Уже есть аккаунт?
            <a href="{{ route('login') }}">
                Войти
            </a>
        </div>
    </form>

@endsection
