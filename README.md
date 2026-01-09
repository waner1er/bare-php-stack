# Bare PHP Stack

Un framework PHP minimaliste et moderne pour l'apprentissage et le développement rapide d'applications web.

## 📋 Description

Bare PHP Stack est un framework PHP léger qui combine les meilleures pratiques modernes avec une architecture simple et compréhensible. Il utilise des composants Illuminate (Laravel) pour les vues tout en maintenant un cœur personnalisé pour le routing, les contrôleurs et les modèles.

## ✨ Fonctionnalités

- 🚀 **Routing avec Attributes PHP** - Définition des routes directement dans les contrôleurs via des attributs PHP 8
- 🎨 **Moteur de templates Blade** - Utilisation du moteur de templates Illuminate/View
- 🗄️ **Système de migrations** - Gestion des schémas de base de données
- 🌱 **Seeders** - Peuplement de la base de données avec des données de test
- 🧩 **Composants réutilisables** - Système de composants pour une architecture modulaire
- 🛠️ **CLI intégré (minor)** - Outil en ligne de commande pour la génération de code et la gestion
- 📝 **Session management** - Gestion des sessions PHP
- 🐛 **Mode debug** - Outils de débogage intégrés

## 📦 Prérequis

- PHP 8.0 ou supérieur
- Composer
- Serveur web (Apache, Nginx) ou Laravel Valet
- Extension PHP PDO pour la base de données

## 🚀 Installation

1. **Cloner le dépôt**
```bash
git clone https://github.com/waner1er/bare-php-stack.git
cd bare-php-stack
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
```
Éditez le fichier `.env` avec vos paramètres de base de données et d'application.

4. **Créer la base de données**
```bash
php minor db:create
```

5. **Exécuter les migrations**
```bash
php minor migrate
```

6. **Peupler la base de données (optionnel)**
```bash
php minor db:seed
```

## 🏃 Démarrage

### Avec Laravel Valet
```bash
valet link
valet secure bare-php-stack  # optionnel, pour HTTPS
```
Accédez à `http://bare-php-stack.test`

### Avec le serveur intégré PHP
```bash
cd public
php -S localhost:8000
```
Accédez à `http://localhost:8000`

## 🛠️ CLI (minor)

Le framework inclut un outil CLI nommé `minor` pour faciliter le développement :

### Génération de code
```bash
# Créer un contrôleur
php minor make:controller NomController

# Créer un modèle
php minor make:model NomModele

# Créer un composant
php minor make:component NomComposant

# Créer un seeder
php minor make:seeder NomSeeder
```

### Gestion de la base de données
```bash
# Créer la base de données
php minor db:create

# Supprimer la base de données
php minor db:drop

# Exécuter les migrations
php minor migrate

# Exécuter les seeders
php minor db:seed
```

### Maintenance
```bash
# Nettoyer le cache
php minor cache:clear

# Nettoyer les sessions
php minor session:clean
```

## 📁 Structure du projet

```
bare-php-stack/
├── public/              # Point d'entrée web
│   └── index.php       # Fichier principal
├── src/
│   ├── Attribute/      # Attributs PHP (ex: Route)
│   ├── Cli/            # Commandes CLI
│   ├── Component/      # Composants réutilisables
│   ├── Controller/     # Contrôleurs
│   ├── Model/          # Modèles
│   ├── Router/         # Système de routing
│   └── Tools/          # Utilitaires et helpers
├── resources/
│   └── views/          # Templates Blade
├── migrations/         # Migrations de base de données
│   ├── files/          # Fichiers de migration
│   └── seeders/        # Seeders
├── minor               # CLI de gestion
├── composer.json       # Dépendances PHP
└── .valet.yaml         # Configuration Valet
```

## 📝 Exemple d'utilisation

### Créer un contrôleur avec routing

```php
<?php

namespace App\Controller;

use App\Attribute\Route;

class PostController extends BaseController
{
    #[Route('/posts', methods: ['GET'])]
    public function index(): void
    {
        $posts = Post::all();
        $this->render('posts.index', ['posts' => $posts]);
    }

    #[Route('/posts/:id', methods: ['GET'])]
    public function show(int $id): void
    {
        $post = Post::find($id);
        $this->render('posts.show', ['post' => $post]);
    }
}
```

### Créer une vue Blade

```blade
<!-- resources/views/posts/index.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Articles</h1>
    @foreach($posts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <p>{{ $post->content }}</p>
        </article>
    @endforeach
@endsection
```

## ⚙️ Configuration

### Variables d'environnement (.env)

```env
APP_DEBUG=true

DB_HOST=localhost
DB_NAME=votre_base
DB_USER=votre_utilisateur
DB_PASS=votre_mot_de_passe
```

## 📚 Dépendances principales

- **vlucas/phpdotenv** - Gestion des variables d'environnement
- **fakerphp/faker** - Génération de données factices
- **illuminate/view** - Moteur de templates Blade
- **illuminate/events** - Système d'événements
- **illuminate/filesystem** - Gestion des fichiers

## 🤝 Contribution

Ce projet est principalement à but éducatif. Les contributions sont les bienvenues !

## 👤 Auteur

**waner1er**
- Email: riveterwan8@gmail.com
- GitHub: [@waner1er](https://github.com/waner1er)

## 📄 Licence

Ce projet est sous licence libre pour l'apprentissage et le développement.

---

⚠️ **Note**: Ce README est provisoire et sera complété au fur et à mesure du développement du projet.
