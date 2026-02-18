<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    {{-- Tabler CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column">

<div class="page page-center">
    <div class="container container-tight py-4">

        {{-- ЛОГО --}}
        <div class="text-center mb-4">
            <h1 class="h2">{{ config('app.name') }}</h1>
        </div>

        {{-- КАРТОЧКА --}}
        <div class="card card-md">
            <div class="card-body">
                @yield('content')
            </div>
        </div>

        <div class="text-center text-muted mt-3">
            © {{ date('Y') }} ПомниКорни
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
</body>
</html>
