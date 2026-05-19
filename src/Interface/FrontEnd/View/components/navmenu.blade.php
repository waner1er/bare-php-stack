<button
    type="button"
    class="nav-toggle"
    data-nav-toggle
    aria-expanded="false"
    aria-controls="primary-nav"
    aria-label="Ouvrir le menu">
    <span></span>
    <span></span>
    <span></span>
</button>
<nav>
    <ul id="primary-nav" class="nav-menu" data-nav-menu>
        @foreach ($menuItems as $item)
            <li class="nav-menu__item">
                @if (isset($item['route']))
                    <a class="nav-menu__link" href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                @else
                    <a class="nav-menu__link" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @endif
            </li>
        @endforeach

        <li class="nav-menu__item">
            @if ($isAuthenticated)
                <span class="nav-menu__greeting">Bonjour {{ $user->getFirstName() }} !</span>
                <a class="nav-menu__link" href="/logout">Déconnexion</a>
            @else
                <a class="nav-menu__link" href="/login">Connexion</a>
                <a class="nav-menu__link" href="/register">Inscription</a>
            @endif
        </li>
    </ul>
</nav>
