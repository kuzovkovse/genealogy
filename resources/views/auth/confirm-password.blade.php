@extends('layouts.guest')

@section('title', 'Подтверждение пароля')

@section('content')

    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <h1 class="h2">{{ config('app.name') }}</h1>
            <div class="text-muted mt-1">
                Подтвердите пароль для продолжения
            </div>
        </div>

        <div class="card card-md">
            <div class="card-body">

                <p class="text-muted small mb-4">
                    Это защищённая зона приложения. Подтвердите пароль.
                </p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required
                               autocomplete="current-password">

                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary">
                            Подтвердить
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <div class="text-center text-muted mt-3">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>

@endsection
