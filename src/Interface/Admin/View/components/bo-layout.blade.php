<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }

        .sidebar h1 {
            font-size: 24px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar nav a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar nav a.active {
            background: #3498db;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.3s;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <h1>🔧 Admin Panel</h1>
        <nav>
            <a href="/admin">📊 Dashboard</a>
            <a href="/admin/posts">📝 Gestion du menu</a>
            <a href="/admin/users">👥 Utilisateurs</a>
            <a href="/">🌐 Voir le site</a>
            <a href="/logout">🚪 Déconnexion</a>
        </nav>
    </aside>

    <div class="main-content">
        <div class="header">
            <h2>Back Office</h2>
            <div>
                Connecté en tant qu'administrateur
            </div>
        </div>

        <div class="content">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
