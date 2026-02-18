<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Текущий пароль</label>
        <input type="password"
               name="current_password"
               class="form-control"
               required>
        @error('current_password')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Новый пароль</label>
        <input type="password"
               name="password"
               class="form-control"
               required>
        @error('password')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Подтверждение пароля</label>
        <input type="password"
               name="password_confirmation"
               class="form-control"
               required>
    </div>

    <button class="btn btn-primary">
        🔐 Обновить пароль
    </button>

    @if (session('status') === 'password-updated')
        <span class="text-success small ms-3">
            Пароль обновлён
        </span>
    @endif
</form>
