<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Genealogy')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- GLightbox --}}
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>


   @stack('styles')
</head>
<body class="bg-light">

{{-- NAV --}}
@include('layouts.navigation')

{{-- flash --}}
@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    </div>
@endif

{{-- CONTENT --}}
<main class="py-4" id="app-main">
    @hasSection('fullscreen')
        @yield('content')
    @else
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            zoomable: true,
            draggable: true,
            preload: true,
            autoplayVideos: false,
            moreText: 'Подробнее',
            slideEffect: 'zoom',
            openEffect: 'zoom',
            closeEffect: 'fade',
        });
    });
</script>
</body>
</html>
