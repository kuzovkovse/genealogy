@extends('layouts.guest')

@section('content')

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus>

            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">
                Отправить ссылку сброса
            </button>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}">
                Назад ко входу
            </a>
        </div>
    </form>

@endsection
