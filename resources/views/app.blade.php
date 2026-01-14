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
    
    <!-- Favicon & Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/icon-180x180.png') }}">
    <meta name="theme-color" content="#1A77C9">

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Paynes">
    <meta name="application-name" content="Paynes">
    <meta name="msapplication-TileColor" content="#1A77C9">

    <!-- iOS Splash Screens -->
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-640x1136.png') }}" media="(device-width: 320px) and (device-height: 568px)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-750x1334.png') }}" media="(device-width: 375px) and (device-height: 667px)">
    <link rel="apple-touch-startup-image" href="{{ asset('icons/splash-1242x2208.png') }}" media="(device-width: 414px) and (device-height: 736px)">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered:', registration.scope);

                        // Проверяем обновления каждый час
                        setInterval(() => {
                            registration.update();
                        }, 60 * 60 * 1000);
                    })
                    .catch((error) => {
                        console.log('SW registration failed:', error);
                    });
            });

            // Уведомление о новой версии
            let refreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            });
        }

        // Обработка события установки PWA
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Показываем кнопку установки (если есть)
            const installBtn = document.getElementById('pwa-install-btn');
            if (installBtn) {
                installBtn.style.display = 'block';
                installBtn.addEventListener('click', () => {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('PWA installed');
                        }
                        deferredPrompt = null;
                    });
                });
            }
        });

        // Трекинг успешной установки
        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed');
            deferredPrompt = null;
        });
    </script>
</body>
</html>
