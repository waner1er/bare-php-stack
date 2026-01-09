<nav>
    <ul>
        <li><a href="{{ route('home') }}">Accueil</a></li>

        @if ($isAuthenticated)
            <li>Bonjour {{ $user['first_name'] ?? 'Utilisateur' }} !</li>
            <li><a href="/logout">Déconnexion</a></li>
        @else
            <li><a href="/login">Connexion</a></li>
            <li><a href="/register">Inscription</a></li>
        @endif
    </ul>
</nav>
