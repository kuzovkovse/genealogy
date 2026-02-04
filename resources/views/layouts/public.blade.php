<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tabler / Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet">

    <style>
        body {
            background: #f6f7f9;
        }
        .public-container {
            max-width: 900px;
            margin: 40px auto;
        }
    </style>
</head>
<body>

<div class="public-container">
    @yield('content')
</div>

</body>
</html>
