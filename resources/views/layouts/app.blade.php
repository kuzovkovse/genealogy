<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'ПомниКорни')</title>

    {{-- Tabler CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet">

    {{-- GLightbox --}}
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    @stack('styles')
</head>

<body>

<div class="page">

    {{-- HEADER --}}
    @include('layouts.navigation')

    <div class="page-wrapper">

        {{-- FLASH --}}
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

        {{-- PAGE BODY --}}
        <div class="page-body py-4">

            @if(View::hasSection('fullscreen'))
                {{-- Полноэкранный режим (например дерево) --}}
                @yield('content')
            @else
                <div class="container-xl">
                    @yield('content')
                </div>
            @endif

        </div>

    </div>
</div>

{{-- Tabler JS --}}
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            zoomable: true
        });
    });
</script>

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
