<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Rewards System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎁</text></svg>">

    <!-- Meta Tags -->
    <meta name="description" content="Sistema de recompensas y alianzas exclusivas">
    <meta name="author" content="Rewards System">
    <meta name="robots" content="noindex, nofollow">

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <!-- Vue App Mount Point -->
    <div id="app"></div>

    <!-- Noscript Fallback -->
    <noscript>
        <div style="
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            text-align: center;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            font-family: 'Inter', sans-serif;
        ">
            <div>
                <h1 style="font-size: 2rem; margin-bottom: 1rem;">⚠️ JavaScript Requerido</h1>
                <p style="font-size: 1.125rem; max-width: 600px; margin: 0 auto;">
                    Esta aplicación requiere JavaScript para funcionar correctamente.<br>
                    Por favor, habilita JavaScript en tu navegador y recarga la página.
                </p>
            </div>
        </div>
    </noscript>
</body>
</html>
