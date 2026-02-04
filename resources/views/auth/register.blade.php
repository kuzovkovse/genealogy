<x-guest-layout>

    <div class="login-title">
        Регистрация
    </div>

    <div class="login-subtitle">
        Создайте аккаунт, чтобы начать<br>
        хранить историю своей семьи
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Имя --}}
        <div class="mb-4">
            <x-input-label for="name" value="Имя" />
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Пароль --}}
        <div class="mb-4">
            <x-input-label for="password" value="Пароль" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Подтверждение пароля --}}
        <div class="mb-6">
            <x-input-label for="password_confirmation" value="Подтверждение пароля" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- КНОПКА РЕГИСТРАЦИИ --}}
        <button
            type="submit"
            style="
                background:#1f2937;
                color:#ffffff;
                width:100%;
                padding:14px;
                border-radius:14px;
                font-weight:600;
                letter-spacing:.02em;
                box-shadow:0 10px 25px rgba(0,0,0,.15);
            "
            onmouseover="this.style.background='#111827'"
            onmouseout="this.style.background='#1f2937'"
        >
            Создать аккаунт
        </button>

        {{-- ССЫЛКА НА ВХОД --}}
        <div class="text-center mt-6 text-sm text-gray-600">
            Уже есть аккаунт?
            <a
                href="{{ route('login') }}"
                class="font-medium text-gray-900 hover:underline"
            >
                Войти
            </a>
        </div>

    </form>

</x-guest-layout>
