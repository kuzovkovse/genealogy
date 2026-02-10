<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">

        {{-- ЛОГО --}}
        <a class="navbar-brand fw-bold" href="{{ route('people.index') }}">
            ПомниКорни
        </a>

        {{-- TOGGLER --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            {{-- LEFT --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('people.*') ? 'active' : '' }}"
                       href="{{ route('people.index') }}">
                        Люди
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>
            </ul>

            {{-- RIGHT --}}
            @auth
                <ul class="navbar-nav ms-auto align-items-center gap-2">

                    {{-- 🔔 УВЕДОМЛЕНИЯ --}}
                    <li class="nav-item">
                        <x-family-notifications-bell />
                    </li>

                    {{-- 👤 USER --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
                           href="#"
                           role="button"
                           data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    Профиль
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">
                                        Выйти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>

                </ul>
            @endauth

        </div>
    </div>
</nav>
