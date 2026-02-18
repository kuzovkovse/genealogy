<header class="navbar navbar-expand-md navbar-light d-print-none border-bottom bg-white sticky-top">
    <div class="container-xl">

        {{-- LOGO --}}
        <a href="{{ route('people.index') }}" class="navbar-brand">
            <strong>ПомниКорни</strong>
        </a>

        {{-- TOGGLER --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar-menu">

            {{-- LEFT NAV --}}
            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('people.*') ? 'active' : '' }}"
                       href="{{ route('people.index') }}">
                        <span class="nav-link-title">
                            Люди
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <span class="nav-link-title">
                            Dashboard
                        </span>
                    </a>
                </li>

            </ul>

            {{-- RIGHT SIDE --}}
            @auth
                <div class="navbar-nav flex-row order-md-last align-items-center">

                    {{-- 🔔 Notifications --}}
                    <div class="nav-item me-3">
                        <x-family-notifications-bell />
                    </div>

                    {{-- USER DROPDOWN --}}
                    <div class="nav-item dropdown">
                        <a href="#"
                           class="nav-link d-flex lh-1 text-reset p-0"
                           data-bs-toggle="dropdown">

                            <div class="d-none d-xl-block ps-2">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="mt-1 small text-muted">
                                    Профиль
                                </div>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                            <a href="{{ route('profile.edit') }}"
                               class="dropdown-item">
                                Профиль
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST"
                                  action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">
                                    Выйти
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            @endauth

        </div>
    </div>
</header>
