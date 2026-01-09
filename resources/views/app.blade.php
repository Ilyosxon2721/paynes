<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Primary Meta Tags -->
    <title>Paynes — Система управления платежами для агентов</title>
    <meta name="title" content="Paynes — Система управления платежами для агентов">
    <meta name="description" content="Современная платформа для агентов платежных систем. Платежи, обмен валют, кредиты и отчёты — всё в одном месте. Попробуйте бесплатно!">
    <meta name="keywords" content="платежи, агент, paynet, пей эль, обмен валют, касса, кассир, Узбекистан, UZS, USD">
    <meta name="author" content="Paynes">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="Paynes — Система управления платежами">
    <meta property="og:description" content="Полный контроль бизнеса для агентов платежных систем. Платежи, обмен валют, кредиты и отчёты — всё в одном месте.">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:locale:alternate" content="uz_UZ">
    <meta property="og:locale:alternate" content="en_US">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url') }}">
    <meta property="twitter:title" content="Paynes — Система управления платежами">
    <meta property="twitter:description" content="Полный контроль бизнеса для агентов платежных систем.">
    <meta property="twitter:image" content="{{ asset('images/og-image.png') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#667eea">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
