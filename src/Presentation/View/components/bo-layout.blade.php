@php
    use App\Infrastructure\Auth\Auth;
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon site</title>
</head>

<body>
    <header>
        <h1> Backoffice</h1>
    </header>
    <main>
        {{ $slot }}
    </main>
    <footer>
        &copy; {{ date('Y') }} Mon Blog
    </footer>
</body>

</html>
