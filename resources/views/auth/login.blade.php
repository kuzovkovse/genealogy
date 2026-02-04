<x-guest-layout>

    <div class="login-title">
        Вход в аккаунт
    </div>

    <div class="login-subtitle">
        Войдите, чтобы продолжить работу<br>
        с семейным архивом
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
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

        {{-- Remember + Forgot --}}
        <div class="flex items-center justify-between mb-6 text-sm">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                >
                <span class="ml-2 text-gray-600">Запомнить меня</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-gray-500 hover:text-gray-800 transition"
                >
                    Забыли пароль?
                </a>
            @endif
        </div>

        {{-- ✅ КНОПКА ВХОДА (исправленная) --}}
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
            Войти
        </button>

        {{-- REGISTER --}}
        @if (Route::has('register'))
            <div class="text-center mt-6 text-sm text-gray-600">
                Нет аккаунта?
                <a
                    href="{{ route('register') }}"
                    class="font-medium text-gray-900 hover:underline"
                >
                    Зарегистрироваться
                </a>
            </div>
        @endif
    </form>

</x-guest-layout>
