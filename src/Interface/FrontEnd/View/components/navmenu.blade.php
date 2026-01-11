<nav>
    <ul>
        @foreach ($menuItems as $item)
            <li>
                @if (isset($item['route']))
                    <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                @else
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @endif
            </li>
        @endforeach

        <li>
            @if ($isAuthenticated)
                <span>Bonjour {{ $user->getFirstName() }} !</span>
                <a href="/logout">Déconnexion</a>
            @else
                <a href="/login">Connexion</a>
                <a href="/register">Inscription</a>
            @endif
        </li>
    </ul>
</nav>
