# Bare PHP Stack - Portfolio & CMS

> Stack PHP moderne avec architecture DDD, routing par attributs, et CLI de développement

## 📋 Table des matières

- [Présentation](#présentation)
- [Stack Technique](#stack-technique)
- [Architecture](#architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [CLI Minor](#cli-minor)
- [Développement](#développement)
- [Migration & Seeding](#migration--seeding)
- [Assets Frontend](#assets-frontend)
- [Debug](#debug)
- [Guides détaillés](#guides-détaillés)

---

## 🎯 Présentation

Bare PHP Stack est un framework PHP personnalisé construit selon les principes du Domain-Driven Design (DDD). Il propose :

- ✅ **Routing moderne** avec attributs PHP 8+
- ✅ **Architecture DDD** propre et maintenable
- ✅ **CLI de développement** pour générer models, controllers, migrations
- ✅ **Auto-découverte des controllers** (plus besoin de les enregistrer manuellement)
- ✅ **Template engine Blade** (Laravel)
- ✅ **Gestion des assets** avec Vite
- ✅ **Debug avancé** avec Tracy
- ✅ **Migrations & Seeders** pour la base de données

---

## 🛠 Stack Technique

### Backend

- **PHP 8.1+** (attributs, types stricts)
- **Composer** pour la gestion des dépendances
- **MySQL/MariaDB** pour la base de données
- **Blade** (Illuminate/View) pour les templates
- **Tracy** pour le debug et monitoring
- **Faker** pour les données de test

### Frontend

- **Vite** pour le bundling des assets
- **SCSS** pour les styles
- **JavaScript ES6+** (modules)

### DevOps

- **PHP-CS-Fixer** pour le formatage du code
- **Git** pour le versioning

---

## 🏗 Architecture

### Structure DDD

```
src/
├── Application/        # Couche Application (Use Cases, Services)
│   └── Service/
│       └── Command/    # CLI Minor
├── Domain/             # Couche Domain (logique métier)
│   ├── Entity/         # Entités métier
│   ├── Repository/     # Interfaces des repositories
│   ├── Contract/       # Contrats/Interfaces
│   └── Abstract/       # Classes abstraites
├── Infrastructure/     # Couche Infrastructure (implémentation technique)
│   ├── Auth/           # Authentification
│   ├── Database/       # Connexion DB, QueryBuilder
│   ├── Middleware/     # Middlewares (Auth, CSRF, Admin)
│   ├── Repository/     # Implémentations concrètes des repositories
│   ├── Router/         # Router, ControllerLoader
│   ├── Session/        # Gestion des sessions
│   └── Utils/          # Utilitaires (Debug, helpers)
└── Interface/          # Couche Interface (controllers, vues)
    ├── Admin/          # Interface d'administration
    │   ├── Controller/
    │   └── View/
    ├── FrontEnd/       # Interface publique
    │   ├── Assets/     # JS, SCSS
    │   ├── Component/  # Composants Blade
    │   ├── Controller/
    │   └── View/
    ├── API/            # API REST (à venir)
    └── Common/         # Ressources partagées
        └── Attribute/  # Attributs PHP (Route)
```

### Principes clés

1. **Séparation des responsabilités** : chaque couche a un rôle précis
2. **Dépendances vers l'intérieur** : Domain ne dépend de rien
3. **Injection de dépendances** via les repositories
4. **Routing par attributs** : `#[Route('/posts', 'GET')]`

---

## 🚀 Installation

### Prérequis

- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL/MariaDB
- Serveur web (Apache/Nginx)

### Étapes

1. **Cloner le repository**

   ```bash
   git clone <votre-repo-url>
   cd bare-php-stack
   ```

2. **Installer les dépendances PHP**

   ```bash
   composer install
   ```

3. **Installer les dépendances JavaScript**

   ```bash
   npm install
   ```

4. **Créer le fichier `.env`**

   ```bash
   cp .env.example .env
   ```

5. **Configurer la base de données** (`.env`)

   ```env
   DB_HOST=localhost
   DB_NAME=barephpstack
   DB_USER=root
   DB_PASS=root

   APP_DEBUG=true
   APP_ENV=development
   SESSION_LIFETIME=1440
   ```

6. **Créer la base de données**

   ```bash
   mysql -u root -p
   CREATE DATABASE barephpstack;
   exit
   ```

7. **Exécuter les migrations**

   ```bash
   php minor migrate:run
   ```

8. **Seed la base de données** (optionnel)

   ```bash
   php minor seed:run
   ```

9. **Compiler les assets**

   ```bash
   # Développement
   npm run dev

   # Production
   npm run build

   # Watch mode
   npm run watch
   ```

10. **Configurer le serveur web**

    **Apache** : créer un VirtualHost pointant vers `/public`

    ```apache
    <VirtualHost *:80>
        ServerName bare-php-stack.test
        DocumentRoot /path/to/bare-php-stack/public

        <Directory /path/to/bare-php-stack/public>
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
    ```

    **Nginx** :

    ```nginx
    server {
        listen 80;
        server_name bare-php-stack.test;
        root /path/to/bare-php-stack/public;
        index index.php;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
    }
    ```

11. **Ajouter au fichier hosts**

    ```bash
    sudo nano /etc/hosts
    # Ajouter :
    127.0.0.1   bare-php-stack.test
    ```

12. **Accéder au site**
    ```
    http://bare-php-stack.test
    ```

---

## ⚙️ Configuration

### Fichiers de configuration

- **`config/paths.php`** : Définit toutes les constantes de chemins
- **`config/bootstrap.php`** : Point d'entrée, initialisation de l'app
- **`.env`** : Variables d'environnement

### Variables d'environnement importantes

| Variable           | Description         | Valeurs                     |
| ------------------ | ------------------- | --------------------------- |
| `APP_DEBUG`        | Mode debug          | `true`, `false`             |
| `APP_ENV`          | Environnement       | `development`, `production` |
| `DB_HOST`          | Hôte MySQL          | `localhost`                 |
| `DB_NAME`          | Nom BDD             | `barephpstack`              |
| `DB_USER`          | Utilisateur BDD     | `root`                      |
| `DB_PASS`          | Mot de passe BDD    |                             |
| `SESSION_LIFETIME` | Durée session (min) | `1440`                      |

---

## 🔧 CLI Minor

Minor est le CLI de développement du projet. Il permet de générer rapidement du code.

### Commandes disponibles

```bash
# Afficher l'aide
php minor help

# Créer une migration
php minor make:migration create_products_table

# Exécuter les migrations
php minor migrate:run

# Réinitialiser la base de données
php minor migrate:reset

# Exécuter les seeders
php minor seed:run

# Générer un modèle (Entity + Repository + Interface)
php minor make:model Product

# Générer un modèle avec migration
php minor make:model Product --migration

# Générer un controller
php minor make:controller Product
```

### Génération de Models

La commande `make:model` crée automatiquement :

- **Entity** : `src/Domain/Entity/Product.php`
- **Repository Interface** : `src/Domain/Repository/ProductRepositoryInterface.php`
- **Repository** : `src/Infrastructure/Repository/ProductRepository.php`
- **Migration** (optionnel avec `--migration`)

**Exemple :**

```bash
php minor make:model Product --migration

# Répondre aux questions :
# - Table name : products
# - Properties : name:string, price:float, stock:int
```

### Génération de Controllers

La commande `make:controller` crée un controller et vous guide :

1. Choix de l'interface (Admin, FrontEnd, API)
2. Utilisation d'un repository (oui/non)
3. Sélection du modèle

**Exemple :**

```bash
php minor make:controller Product

# Interface : FrontEnd
# Repository : oui
# Modèle : Product
```

Le controller généré utilisera automatiquement le `ProductRepositoryInterface`.

---

## 💻 Développement

### Créer une nouvelle page

1. **Créer le controller**

   ```bash
   php minor make:controller MyPage
   ```

2. **Ajouter les routes avec attributs**

   ```php
   <?php
   namespace App\Interface\FrontEnd\Controller;

   use App\Interface\Common\Attribute\Route;

   class MyPageController
   {
       #[Route('/my-page', 'GET')]
       public function index(): void
       {
           view('mypage.index', [
               'title' => 'Ma Page'
           ]);
       }
   }
   ```

3. **Créer la vue** : `src/Interface/FrontEnd/View/mypage/index.blade.php`

   ```blade
   @component('components.layout', ['pageTitle' => $title])
       <h1>{{ $title }}</h1>
       <p>Contenu de ma page</p>
   @endcomponent
   ```

4. **Accéder à la page** : `http://bare-php-stack.test/my-page`

### Auto-découverte des controllers

**Plus besoin d'enregistrer manuellement les controllers** !

Le `ControllerLoader` scanne automatiquement :

- `src/Interface/FrontEnd/Controller/`
- `src/Interface/Admin/Controller/`
- `src/Interface/API/`

Tous les controllers avec des attributs `#[Route]` sont chargés automatiquement.

### Middlewares disponibles

#### AuthMiddleware

```php
use App\Infrastructure\Middleware\AuthMiddleware;

// Protéger une route (dans le controller)
public function dashboard(): void
{
    AuthMiddleware::handle(); // Redirige vers /login si non connecté
    // ...
}
```

#### CsrfMiddleware

```php
use App\Infrastructure\Middleware\CsrfMiddleware;

// Protéger une action POST
#[Route('/form', 'POST')]
public function submit(): void
{
    CsrfMiddleware::handle(); // Vérifie le token CSRF
    // ...
}

// Dans le formulaire Blade :
{!! CsrfMiddleware::field() !!}
```

#### AdminMiddleware

```php
use App\Infrastructure\Middleware\AdminMiddleware;

// Réserver aux admins
public function admin(): void
{
    AdminMiddleware::handle(); // Vérifie role = 'admin'
    // ...
}
```

### Helpers disponibles

```php
// Afficher une vue Blade
view('posts.index', ['posts' => $posts]);

// Redirection
redirect('/login');

// Générer une URL de route
url('posts.show', ['slug' => 'mon-article']);

// Dump & Die (Tracy)
dump($variable);
bdump($variable, 'Label'); // Dans la barre de debug

// Debug
dd($variable); // Dump and die
```

---

## 🗄 Migration & Seeding

### Migrations

**Créer une migration :**

```bash
php minor make:migration create_products_table
```

**Fichier généré** : `migrations/files/YYYY_MM_DD_HHMMSS_create_products_table.php`

```php
<?php
return [
    'up' => "
        CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ",
    'down' => "DROP TABLE IF EXISTS products;"
];
```

**Exécuter les migrations :**

```bash
php minor migrate:run
```

**Réinitialiser la BDD :**

```bash
php minor migrate:reset  # Rollback toutes les migrations
php minor migrate:run    # Puis les réexécuter
```

### Seeders

**Créer un seeder** : `migrations/seeders/ProductSeeder.php`

```php
<?php
namespace Seeders;

use App\Infrastructure\Database\Database;
use Faker\Factory;

class ProductSeeder
{
    public static function run(): void
    {
        $db = Database::getInstance();
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $db->insert('products', [
                'name' => $faker->word,
                'price' => $faker->randomFloat(2, 10, 1000),
            ]);
        }

        echo "✓ 20 produits créés\n";
    }
}
```

**Exécuter les seeders :**

```bash
php minor seed:run
```

---

## 🎨 Assets Frontend

### Structure

```
src/Interface/FrontEnd/Assets/
├── js/
│   └── app.js          # Point d'entrée JS
└── scss/
    ├── app.scss        # Point d'entrée SCSS
    ├── _variables.scss
    ├── _mixins.scss
    └── components/
```

### Vite Configuration

**`vite.config.js`** :

```js
import { defineConfig } from "vite";

export default defineConfig({
  build: {
    outDir: "public/dist",
    rollupOptions: {
      input: {
        frontend: "src/Interface/FrontEnd/Assets/js/app.js",
        admin: "src/Interface/Admin/Assets/js/app.js",
      },
    },
  },
});
```

### Commandes

```bash
# Mode développement (HMR)
npm run dev

# Build production
npm run build

# Watch mode (recompile automatiquement)
npm run watch
```

### Utilisation dans Blade

```blade
<link rel="stylesheet" href="/dist/css/frontend-style.css">
<script type="module" src="/dist/js/frontend.js"></script>
```

---

## 🐛 Debug

### Tracy Debugger

Tracy est intégré pour le debug avancé.

**Activation** : via `.env`

```env
APP_DEBUG=true
```

**Fonctionnalités :**

- 🔴 **Barre de debug** en bas à droite
- 💥 **BlueScreen** détaillé sur les erreurs
- 📊 **Profiling** des requêtes et performances
- 📝 **Logs** dans `storage/logs/`

**Utilisation :**

```php
// Dump dans la barre de debug
bdump($variable, 'Mon Label');

// Dump et die
dump($variable);
dd($variable);

// Logger
\Tracy\Debugger::log('Message de log');
```

**Production** : Tracy enregistre les erreurs dans `storage/logs/` sans les afficher.

---

## 📚 Guides détaillés

Des guides complets sont disponibles dans le projet :

- **[MAKE_MODEL_GUIDE.md](MAKE_MODEL_GUIDE.md)** : Guide complet pour créer des models
- **[MAKE_CONTROLLER_GUIDE.md](MAKE_CONTROLLER_GUIDE.md)** : Guide complet pour créer des controllers
- **[ENTITY_ARCHIVE_GUIDE.md](ENTITY_ARCHIVE_GUIDE.md)** : Guide pour archiver des entités
- **[REFACTORING_SUMMARY.md](REFACTORING_SUMMARY.md)** : Historique des refactorings

---

## 🔒 Sécurité

### CSRF Protection

Tous les formulaires POST/PUT/DELETE doivent inclure un token CSRF :

```blade
<form method="POST" action="/submit">
    {!! CsrfMiddleware::field() !!}
    <!-- ... -->
</form>
```

### Authentification

```php
use App\Infrastructure\Auth\Auth;

// Vérifier si connecté
if (Auth::check()) {
    $user = Auth::user();
}

// Login
Auth::login($user);

// Logout
Auth::logout();
```

### Sessions

```php
use App\Infrastructure\Session\Session;

Session::set('key', 'value');
$value = Session::get('key');
Session::remove('key');
Session::has('key');
```

---

## 📦 Déploiement

### Checklist production

1. ✅ Mettre `APP_DEBUG=false` dans `.env`
2. ✅ Mettre `APP_ENV=production`
3. ✅ Compiler les assets : `npm run build`
4. ✅ Configurer les permissions :
   ```bash
   chmod -R 755 storage/
   chmod -R 755 storage/logs/
   chmod -R 755 storage/cache/
   ```
5. ✅ Optimiser l'autoload :
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
6. ✅ Configurer le serveur web (voir [Installation](#installation))

---

## 🤝 Contribution

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

**Code Style** : Utilisez PHP-CS-Fixer

```bash
vendor/bin/php-cs-fixer fix
```

---

## 📝 License

Ce projet est sous licence MIT.

---

## 👨‍💻 Auteur

**Erwan** - [waner1er](mailto:riveterwan8@gmail.com)

---

## 🆘 Support

Pour toute question ou problème :

1. Consulter les [guides détaillés](#guides-détaillés)
2. Vérifier les logs dans `storage/logs/`
3. Activer le mode debug (`APP_DEBUG=true`)
4. Ouvrir une issue sur GitHub

---

## 📈 Roadmap

- [ ] API REST complète
- [ ] Tests unitaires et d'intégration
- [ ] Docker pour l'environnement de dev
- [ ] CI/CD avec GitHub Actions
- [ ] Cache système (Redis/Memcached)
- [ ] Queue système pour tâches asynchrones
- [ ] Internationalisation (i18n)

---

**Bon développement ! 🚀**
