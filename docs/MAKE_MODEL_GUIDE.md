# CLI Minor - Génération de Models

## Commande `make:model`

La commande `make:model` a été améliorée pour créer automatiquement une architecture complète incluant :

### Fichiers générés

Quand vous exécutez `php minor make:model Product`, la commande crée automatiquement :

1. **Entity** : `src/Domain/Entity/Product.php`

   - Classe modèle avec propriétés et getters/setters
   - Hérite de `Model` pour les opérations de base de données

2. **Repository Interface** : `src/Domain/Repository/ProductRepositoryInterface.php`

   - Interface définissant les méthodes du repository
   - Méthodes de base : `find()`, `findAll()`, `save()`, `delete()`

3. **Repository Implementation** : `src/Infrastructure/Repository/ProductRepository.php`

   - Implémentation concrète du repository
   - Utilise l'Entity pour les opérations

4. **Migration** (optionnel) : `migrations/files/YYYY_MM_DD_HHMMSS_create_products_table.php`
   - Script SQL pour créer la table en base de données

### Utilisation

```bash
# Création basique
php minor make:model Product

# Avec migration automatique
php minor make:model Product --migration

# Exemple interactif complet
php minor make:model Product
```

### Exemple de workflow

```bash
# 1. Créer le modèle Product
php minor make:model Product --migration

# Répondre aux questions :
# Nom de la table : products (par défaut)
# Propriétés :
#   name (string)
#   description (text)
#   price (float)
#   stock (int)
#   category_id (int, foreign key vers categories)

# 2. Fichiers créés :
# src/Domain/Entity/Product.php
# src/Domain/Repository/ProductRepositoryInterface.php
# src/Infrastructure/Repository/ProductRepository.php
# migrations/files/2026_01_12_HHMMSS_create_products_table.php

# 3. Exécuter la migration
php minor migrate

# 4. Utiliser dans un controller
```

### Exemple d'utilisation dans un Controller

```php
<?php

namespace App\Interface\FrontEnd\Controller;

use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Repository\ProductRepository;
use App\Interface\Common\BaseController;
use App\Interface\Common\Attribute\Route;

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
            echo "Product not found.";
            return;
        }

        $this->render('products.show', ['product' => $product]);
    }
}
```

### Avantages

- **Architecture propre** : Séparation Domain / Infrastructure
- **Testabilité** : Interfaces permettent le mocking
- **Maintenabilité** : Code organisé et standardisé
- **Rapidité** : Tout est généré automatiquement
- **Cohérence** : Tous les models suivent le même pattern

### Bonnes pratiques

1. **Toujours utiliser l'interface dans les controllers**

   ```php
   private ProductRepositoryInterface $productRepository; //  Bon
   private ProductRepository $productRepository;          //  Éviter
   ```

2. **Ajouter des méthodes personnalisées au besoin**

   ```php
   // Dans ProductRepositoryInterface.php
   public function findByCategory(int $categoryId): array;
   public function findInStock(): array;

   // Dans ProductRepository.php
   public function findByCategory(int $categoryId): array
   {
       return Product::where('category_id', $categoryId);
   }
   ```

3. **Utiliser les migrations** pour versionner votre schéma de base de données
   ```bash
   php minor make:model Product --migration
   php minor migrate
   ```

### Autres commandes disponibles

```bash
php minor make:controller ProductController  # Créer un controller
php minor make:component ProductCard         # Créer un composant Blade
php minor make:seeder ProductSeeder          # Créer un seeder
php minor db:seed                            # Exécuter les seeders
php minor cache:clear                        # Vider le cache Blade
```
