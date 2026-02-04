<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ПомниКорни') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    <!-- Optional warm serif for headings -->
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:400,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #ffffff;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .login-logo {
            margin-bottom: 32px;
            animation: fadeInDown 0.8s ease-out both;
        }

        .login-logo img {
            max-width: 420px;
            width: 100%;
            height: auto;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            padding: 28px;
            animation: fadeInUp 0.9s ease-out both;
        }

        .login-title {
            font-family: 'Merriweather', serif;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 6px;
            color: #1f2937;
        }

        .login-subtitle {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .login-footer {
            margin-top: 16px;
            font-size: 12px;
            text-align: center;
            color: #9ca3af;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">

<div class="login-wrapper">

    {{-- LOGO --}}
    <div class="login-logo">
        <img src="{{ asset('storage/brand/logo-login.png') }}"
             alt="ПомниКорни">
    </div>

    {{-- CARD --}}
    <div class="login-card">
        {{ $slot }}

        <div class="login-footer">
            © {{ date('Y') }} ПомниКорни — память, которая остаётся
        </div>
    </div>

</div>

</body>
</html>
