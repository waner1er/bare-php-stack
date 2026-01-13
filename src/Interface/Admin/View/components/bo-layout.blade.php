<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Admin</title>
    <link rel="stylesheet" href="/dist/css/admin-style.css">
    <link rel="stylesheet" href="/dist/css/common-style.css">
    <link rel="stylesheet" href="/dist/css/admin.css">
</head>

<body class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar__header">
            <h1 class="admin-sidebar__logo">🔧 Admin Panel</h1>
        </div>

        <nav class="admin-sidebar__nav">
            <a href="/admin"
                class="admin-sidebar__link {{ $_SERVER['REQUEST_URI'] === '/admin' ? 'admin-sidebar__link--active' : '' }}">
                <span class="admin-sidebar__icon">📊</span>
                <span class="admin-sidebar__text">Dashboard</span>
            </a>
            <a href="/admin/crud/posts"
                class="admin-sidebar__link {{ str_contains($_SERVER['REQUEST_URI'], '/admin/crud/posts') ? 'admin-sidebar__link--active' : '' }}">
                <span class="admin-sidebar__icon">📋</span>
                <span class="admin-sidebar__text">Posts</span>
            </a>
            <a href="/admin/menu"
                class="admin-sidebar__link {{ str_contains($_SERVER['REQUEST_URI'], '/admin/menu') ? 'admin-sidebar__link--active' : '' }}">
                <span class="admin-sidebar__icon">🗂️</span>
                <span class="admin-sidebar__text">Menu</span>
            </a>
            <a href="/admin/users" class="admin-sidebar__link">
                <span class="admin-sidebar__icon">👥</span>
                <span class="admin-sidebar__text">Utilisateurs</span>
            </a>
        </nav>

        <div class="admin-sidebar__footer">
            <a href="/" class="admin-sidebar__link admin-sidebar__link--secondary">
                <span class="admin-sidebar__icon">🌐</span>
                <span class="admin-sidebar__text">Voir le site</span>
            </a>
            <a href="/logout" class="admin-sidebar__link admin-sidebar__link--danger">
                <span class="admin-sidebar__icon">🚪</span>
                <span class="admin-sidebar__text">Déconnexion</span>
            </a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar__left">
                <button class="admin-topbar__menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h2 class="admin-topbar__title">{{ $pageTitle ?? 'Administration' }}</h2>
            </div>

            <div class="admin-topbar__right">
                <div class="admin-topbar__user">
                    <span class="admin-topbar__user-name">Administrateur</span>
                    <span class="admin-topbar__user-badge">Admin</span>
                </div>
            </div>
        </header>

        <main class="admin-content">
            {{ $slot }}
        </main>

        <footer class="admin-footer">
            <p class="admin-footer__text">© {{ date('Y') }} - Back Office Administration</p>
        </footer>
    </div>

    <script type="module" src="/dist/js/admin.js"></script>
    <script type="module" src="/dist/js/common.js"></script>
</body>

</html>
