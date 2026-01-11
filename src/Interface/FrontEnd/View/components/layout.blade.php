@php
    use App\Infrastructure\Auth\Auth;
    use App\Interface\FrontEnd\Component\NavMenu;

    $navMenu = new NavMenu();
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon site</title>
</head>

<body>
    <header>
        <h1>Mon Blog</h1>
        {!! $navMenu->render() !!}
    </header>
    <main>
        {{ $slot }}
    </main>
    <footer>
        &copy; {{ date('Y') }} Mon Blog
    </footer>
</body>

</html>
