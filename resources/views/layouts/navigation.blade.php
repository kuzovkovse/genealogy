<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">

        {{-- 🔷 BRAND --}}
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('people.index') }}" class="text-decoration-none text-dark">
                ПомниКорни
            </a>
        </h1>

        {{-- 🔷 TOGGLER (mobile) --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbar-menu"
                aria-controls="navbar-menu"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- 🔷 RIGHT SIDE --}}
        <div class="navbar-nav flex-row order-md-last">

            {{-- 🔔 Уведомления --}}
            <div class="nav-item me-3">
                <x-family-notifications-bell />
            </div>

            {{-- 👤 USER --}}
            @auth
                <div class="nav-item dropdown">
                    <a href="#"
                       class="nav-link d-flex lh-1 text-reset p-0"
                       data-bs-toggle="dropdown"
                       aria-label="Open user menu">

                        <span class="avatar avatar-sm"
                              style="background-image: url({{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }})">
                        </span>

                        <div class="d-none d-xl-block ps-2">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="mt-1 small text-muted">Профиль</div>
                        </div>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            Профиль
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger">
                                Выйти
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        {{-- 🔷 LEFT MENU --}}
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav">

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
        </div>

    </div>
</header>
