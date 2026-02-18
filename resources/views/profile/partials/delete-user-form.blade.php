<form method="POST"
      action="{{ route('profile.destroy') }}"
      onsubmit="return confirm('Вы уверены, что хотите удалить аккаунт?')">

    @csrf
    @method('DELETE')

    <div class="mb-3">
        <label class="form-label">Введите пароль для подтверждения</label>
        <input type="password"
               name="password"
               class="form-control"
               required>
        @error('password')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-danger">
        🗑 Удалить аккаунт
    </button>
</form>
