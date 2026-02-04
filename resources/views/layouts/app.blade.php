<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Genealogy')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-light">

{{-- NAV --}}
@include('layouts.navigation')

{{-- CONTENT --}}
<main class="py-6" id="app-main">
    @hasSection('fullscreen')
        {{-- fullscreen-страницы (дерево) --}}
        @yield('content')
    @else
        {{-- обычные страницы --}}
        <div class="container">
            @yield('content')
        </div>
    @endif
</main>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

@if(app()->environment('dev'))
    <div style="
        position:fixed;
        bottom:10px;
        right:10px;
        background:#dc3545;
        color:white;
        padding:6px 10px;
        font-size:12px;
        border-radius:6px;
        z-index:9999;
        font-weight:bold;
    ">
        DEV
    </div>
@endif
</body>
</html>
