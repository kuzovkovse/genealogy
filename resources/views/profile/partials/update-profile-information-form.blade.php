<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label">Имя</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', $user->name) }}"
               required>
        @error('name')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email"
               name="email"
               class="form-control"
               value="{{ old('email', $user->email) }}"
               required>
        @error('email')
        <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-primary">
        💾 Сохранить
    </button>

    @if (session('status') === 'profile-updated')
        <span class="text-success small ms-3">
            Сохранено
        </span>
    @endif
</form>
