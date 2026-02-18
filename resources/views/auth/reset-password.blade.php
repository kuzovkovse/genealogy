@extends('layouts.guest')

@section('content')

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $request->email) }}"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Новый пароль</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Подтверждение</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control"
                   required>
        </div>

        <div class="form-footer">
            <button class="btn btn-primary w-100">
                Сбросить пароль
            </button>
        </div>

    </form>

@endsection
