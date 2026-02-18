<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Genealogy')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tabler --}}
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet">

    @stack('styles')
</head>
<body>

<div class="page">

    {{-- NAVIGATION --}}
    @include('layouts.navigation')

    <div class="page-wrapper">

        {{-- Flash --}}
        @if(session('success'))
            <div class="container-xl mt-3">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container-xl mt-3">
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- CONTENT --}}
        @hasSection('fullscreen')
            @yield('content')
        @else
            <div class="container-xl py-4">
                @yield('content')
            </div>
        @endif

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>

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
        font-weight:bold;">
        DEV
    </div>
@endif

</body>
</html>
