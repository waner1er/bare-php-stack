# Bare PHP Stack

Un petit framework PHP écrit à la main, sans Laravel ni Symfony, pour comprendre ce qu'ils font sous le capot : routing, conteneur de vues, repositories, CLI, migrations.

Je m'en sers aussi comme base de départ pour des petits sites (blog, CMS léger) quand un gros framework serait disproportionné. Le projet embarque un back-office avec CRUD auto-généré, gestion de menu et éditeur TinyMCE.

## Ce qu'il y a dedans

- Routing déclaré avec les attributs PHP 8 (`#[Route('/admin/menu', 'GET')]`), controllers découverts automatiquement
- Clean architecture en quatre couches : `Domain`, `Application`, `Infrastructure`, `Interface`
- Entités sans dépendance à la base de données, persistance via des repositories derrière des interfaces du Domain
- Vues Blade (via `illuminate/view`), assets compilés avec Vite et Sass
- Sessions en base, middlewares d'authentification, d'administration et CSRF
- Une CLI maison, `minor`, pour générer du code et gérer la base
- Des tests d'architecture PHPUnit qui vérifient que les couches respectent leurs dépendances

## Installation

Prérequis : PHP 8.3+, Composer, Node.js 18+, MySQL ou MariaDB.

```bash
git clone https://github.com/waner1er/bare-php-stack.git
cd bare-php-stack
composer install
npm install
cp .env.example .env        # puis renseigner les accès à la base
php minor db:create
php minor migrate
php minor db:seed           # données de démo, optionnel
npm run build
```

Le dossier public est `public/`. En local j'utilise Laravel Valet (`.valet.yaml` est fourni), mais n'importe quel vhost Apache ou Nginx pointant sur `public/` avec réécriture vers `index.php` fonctionne.

## La CLI `minor`

```bash
php minor make:model Product --migration   # entité + interface de repository + repository (+ migration)
php minor make:controller Product          # demande l'interface cible (Admin, FrontEnd) et le repository à injecter
php minor make:crud Product                # ressource CRUD disponible sur /admin/crud/products
php minor make:component Card              # composant Blade, avec classe si besoin
php minor make:seeder Product

php minor db:create
php minor db:drop [--force]
php minor migrate
php minor migrate:fresh [--seed]
php minor db:seed

php minor cache:clear
php minor session:clean
```

Sans argument, `php minor` affiche la liste des commandes.

## Organisation du code

```
src/
├── Domain/           entités, interfaces de repositories, contrats
├── Application/      services applicatifs (moteur de CRUD)
├── Infrastructure/   base de données, repositories, router, auth, sessions, middlewares
└── Interface/
    ├── Admin/        back-office : controllers, vues, assets
    ├── FrontEnd/     site public
    ├── Cli/          commandes de minor
    └── Common/       attribut Route
```

La règle principale : le Domain ne dépend de rien. Les tests dans `tests/Architecture` le vérifient, ainsi que le fait que chaque repository implémente une interface du Domain et que les entités restent de simples objets PHP.

```bash
composer test
```

## Front

```bash
npm run dev      # build en continu
npm run serve    # serveur Vite avec HMR
npm run build    # build de production dans public/dist
```

## En cours / à venir

- Envoi du formulaire de contact
- Remplacement des `onclick` inline de la page menu par des data-attributes
- Tests d'intégration sur les repositories
- Environnement Docker

## Licence

MIT, voir [LICENSE](LICENSE).

Erwan Rivet, [waner1er](https://github.com/waner1er)
