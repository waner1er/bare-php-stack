@php
    use App\Tools\Auth;
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
        @include('components.navmenu', ['isAuthenticated' => Auth::check(), 'user' => Auth::user()])
    </header>
    <main>
        {{ $slot }}
    </main>
    <footer>
        &copy; {{ date('Y') }} Mon Blog
    </footer>
</body>

</html>
