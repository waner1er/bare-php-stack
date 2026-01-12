@php
    use App\Infrastructure\Auth\Auth;
    use App\Interface\FrontEnd\Component\NavMenu;

    $navMenu = new NavMenu();
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Mon Portfolio' }}</title>
    <link rel="stylesheet" href="/dist/css/frontend-style.css">
    <link rel="stylesheet" href="/dist/css/common-style.css">
</head>

<body>
    <header class="site-header">
        <div class="site-header__container">
            <div class="site-header__logo">Mon Portfolio</div>
            {!! $navMenu->render() !!}
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <p class="site-footer__text">&copy; {{ date('Y') }} Mon Portfolio - Tous droits réservés</p>
    </footer>

    <script type="module" src="/dist/js/frontend.js"></script>
    <script type="module" src="/dist/js/common.js"></script>
</body>

</html>
