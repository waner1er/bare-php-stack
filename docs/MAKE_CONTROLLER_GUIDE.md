# CLI Minor - Génération de Controllers

## Commande `make:controller`

La commande `make:controller` a été améliorée pour créer automatiquement des controllers utilisant les repository interfaces !

### Utilisation

```bash
php minor make:controller Product
```

### Workflow interactif

La commande vous guide à travers plusieurs étapes :

1. **Choix de l'interface** (Admin, FrontEnd, API, etc.)
2. **Utilisation d'un repository** (oui/non)
3. **Sélection du modèle** (si repository choisi)

### Exemple complet

```bash
$ php minor make:controller Product

Choisissez l'interface cible :
  [1] Admin
  [2] API
  [3] FrontEnd
Votre choix : 3

Voulez-vous utiliser un repository ? (o/n) : o

Choisissez le modèle à utiliser :
  [1] Category
  [2] MenuItem
  [3] Post
  [4] Product
  [5] User
  [0] Aucun (créer un controller vide)
Votre choix : 4

✓ Controller ProductController créé dans Interface/FrontEnd/Controller.
```

### Code généré

#### Avec Repository (Product)

```php
<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Repository\ProductRepository;
use App\Domain\Entity\Product;

class ProductController extends BaseController
{
    private ProductRepositoryInterface $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    #[Route('/products', 'GET', 'products.index')]
    public function index(): void
    {
        $products = $this->productRepository->findAll();
        $this->render('products.index', ['products' => $products]);
    }

    #[Route('/products/{id}', 'GET', 'products.show')]
    public function show(int $id): void
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            http_response_code(404);
            echo "Product non trouvé.";
            return;
        }

        $this->render('products.show', ['product' => $product]);
    }
}
```

#### Sans Repository (vide)

```php
<?php

declare(strict_types=1);

namespace App\Interface\FrontEnd\Controller;

use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;

class CustomController extends BaseController
{
}
```

### Avantages

- **Génération automatique des routes** avec attributs PHP 8
- **Injection du repository interface** (bonne pratique)
- **Méthodes index() et show() pré-configurées**
- **Gestion d'erreur 404 incluse**
- **Nommage cohérent** des variables et routes

### Workflow complet

```bash
# 1. Créer le modèle avec repository
php minor make:model Product --migration

# 2. Créer le controller utilisant ce modèle
php minor make:controller Product
# Choisir : FrontEnd > Oui (repository) > Product

# 3. Exécuter la migration
php minor migrate

# 4. Créer les vues Blade correspondantes
# resources/views/products/index.blade.php
# resources/views/products/show.blade.php

# 5. C'est prêt !
```

### Méthodes générées

Les controllers avec repository incluent automatiquement :

#### `index()` - Liste tous les éléments

```php
#[Route('/products', 'GET', 'products.index')]
public function index(): void
{
    $products = $this->productRepository->findAll();
    $this->render('products.index', ['products' => $products]);
}
```

#### `show($id)` - Affiche un élément

```php
#[Route('/products/{id}', 'GET', 'products.show')]
public function show(int $id): void
{
    $product = $this->productRepository->find($id);

    if (!$product) {
        http_response_code(404);
        echo "Product non trouvé.";
        return;
    }

    $this->render('products.show', ['product' => $product]);
}
```

### Ajouter des méthodes personnalisées

Vous pouvez facilement étendre le controller :

```php
#[Route('/products/create', 'GET', 'products.create')]
public function create(): void
{
    $this->render('products.create');
}

#[Route('/products', 'POST', 'products.store')]
public function store(): void
{
    $product = new Product([
        'name' => $_POST['name'],
        'price' => $_POST['price'],
        // ...
    ]);

    $this->productRepository->save($product);

    header('Location: /products');
}

#[Route('/products/{id}/edit', 'GET', 'products.edit')]
public function edit(int $id): void
{
    $product = $this->productRepository->find($id);

    if (!$product) {
        http_response_code(404);
        return;
    }

    $this->render('products.edit', ['product' => $product]);
}

#[Route('/products/{id}', 'PUT', 'products.update')]
public function update(int $id): void
{
    $product = $this->productRepository->find($id);

    if (!$product) {
        http_response_code(404);
        return;
    }

    $product->setName($_POST['name']);
    $product->setPrice($_POST['price']);

    $this->productRepository->save($product);

    header('Location: /products/' . $id);
}

#[Route('/products/{id}', 'DELETE', 'products.destroy')]
public function destroy(int $id): void
{
    $product = $this->productRepository->find($id);

    if (!$product) {
        http_response_code(404);
        return;
    }

    $this->productRepository->delete($product);

    header('Location: /products');
}
```

### Architecture générée

```
src/Interface/FrontEnd/
└── Controller/
    └── ProductController.php  ← Utilise
                                  ↓
src/Domain/Repository/
└── ProductRepositoryInterface.php  ← Interface
                                       ↓
src/Infrastructure/Repository/
└── ProductRepository.php  ← Implémentation
                             ↓
src/Domain/Entity/
└── Product.php  ← Modèle
```

### Bonnes pratiques

1. **Toujours utiliser l'interface dans le type**

   ```php
   private ProductRepositoryInterface $repo; //
   private ProductRepository $repo;          //
   ```

2. **Un controller = Un modèle principal**

   - ProductController → Product
   - UserController → User

3. **Nommage cohérent**

   - Routes : `/products` (pluriel, minuscules)
   - Variables : `$products`, `$product` (camelCase)
   - Vues : `products.index`, `products.show`

4. **Ajouter la validation avant save()**
   ```php
   if (empty($_POST['name'])) {
       http_response_code(400);
       echo "Le nom est requis";
       return;
   }
   ```

### Commandes connexes

```bash
php minor make:model Product --migration     # Créer model + repository
php minor make:controller Product            # Créer controller
php minor make:component ProductCard --class # Créer composant
php minor make:seeder ProductSeeder          # Créer seeder
```

### Exemple de vue Blade

**products/index.blade.php**

```blade
@extends('layouts.app')

@section('content')
    <h1>Produits</h1>

    <div class="products-grid">
        @foreach($products as $product)
            <div class="product-card">
                <h2>{{ $product->getName() }}</h2>
                <p>Prix : {{ $product->getPrice() }}€</p>
                <a href="/products/{{ $product->getId() }}">Voir</a>
            </div>
        @endforeach
    </div>
@endsection
```

**products/show.blade.php**

```blade
@extends('layouts.app')

@section('content')
    <h1>{{ $product->getName() }}</h1>
    <p>{{ $product->getDescription() }}</p>
    <p>Prix : {{ $product->getPrice() }}€</p>
    <a href="/products">Retour</a>
@endsection
```
