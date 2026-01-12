# Guide : Ajouter une nouvelle entité avec gestion de menu et archive

## Exemple : Ajouter une entité "Product" (Produit)

### 1. Créer l'entité Product

```php
// src/Domain/Entity/Product.php
class Product extends Model implements SlugResourceInterface
{
    public int $id;
    public string $name;
    public string $slug;
    public string $description;
    public ?int $category_id = null;

    // Implémenter SlugResourceInterface
    public static function getAllSlugsForMenu(): array
    {
        $products = static::all();
        return array_map(fn($p) => [
            'slug' => $p->getSlug(),
            'title' => $p->getName(),
            'type' => 'product'
        ], $products);
    }
}
```

### 2. Créer le Repository

Utiliser la commande CLI :

```bash
php minor make:model Product
```

Cela crée automatiquement :

- `src/Domain/Entity/Product.php`
- `src/Domain/Repository/ProductRepositoryInterface.php`
- `src/Infrastructure/Repository/ProductRepository.php`

### 3. Modifier le PostController pour supporter Product

```php
// src/Interface/FrontEnd/Controller/PostController.php

use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Repository\ProductRepository;

public function __construct(
    private PostRepositoryInterface $postRepository = new PostRepository(),
    private CategoryRepositoryInterface $categoryRepository = new CategoryRepository(),
    private ProductRepositoryInterface $productRepository = new ProductRepository(), // AJOUTER
) {}

// Dans la méthode archive(), décommenter la ligne Product :
$repository = match($entityType) {
    'Post' => $this->postRepository,
    'Product' => $this->productRepository, // DÉCOMMENTER
    default => $this->postRepository,
};

// Et ajouter dans les vues :
$viewName = match($entityType) {
    'Post' => 'archive',
    'Product' => 'products.archive', // DÉCOMMENTER (créer la vue si besoin)
    default => 'archive',
};
```

### 4. Ajouter Product dans MenuManager

```php
// src/Interface/FrontEnd/Component/MenuManager.php

public static function getAllMenuSlugs(): array
{
    $resources = [
        Post::class,
        Product::class, // AJOUTER
    ];

    // Le reste du code reste identique
}
```

### 5. Créer une vue dédiée (optionnel)

Si vous voulez une vue spécifique pour les produits :

```php
// src/Interface/FrontEnd/View/products/archive.blade.php
@extends('archive')

@section('item-card')
    <div class="product-card">
        <!-- Design spécifique aux produits -->
        <h3>{{ $item->getName() }}</h3>
        <p>{{ $item->getDescription() }}</p>
        <span class="price">{{ $item->getPrice() }}€</span>
    </div>
@endsection
```

### 6. Modifier le gestionnaire de menu (Admin)

```php
// src/Interface/Admin/View/menu/index.blade.php

// Ajouter un sélecteur de type d'entité
<select name="entity_type">
    <option value="Post">Articles</option>
    <option value="Product">Produits</option>
</select>
```

### 7. Utilisation dans le menu

Maintenant, dans le back-office `/admin/menu`, vous pouvez :

1. **Type "Article individuel"** → Choisir un article spécifique
   - URL : `/slug-article`
2. **Type "Liste d'articles"** →

   - **Tous les articles** : URL `/posts` ou `/archive?entity=Post`
   - **Catégorie spécifique** : URL `/posts/categorie-slug`

3. **Type "Liste de produits"** (après ajout de Product) →
   - **Tous les produits** : URL `/archive?entity=Product`
   - **Catégorie de produits** : URL `/products/categorie-slug`

### 8. Récapitulatif des URLs

| Type                   | Exemple URL                | Description               |
| ---------------------- | -------------------------- | ------------------------- |
| Post individuel        | `/a-propos`                | Page article dans le menu |
| Post normal            | `/posts/mon-article`       | Article blog standard     |
| Archive posts          | `/posts` ou `/archive`     | Tous les articles         |
| Posts par catégorie    | `/posts/developpement-web` | Articles d'une catégorie  |
| Archive produits       | `/archive?entity=Product`  | Tous les produits         |
| Produits par catégorie | `/products/electronique`   | Produits d'une catégorie  |

### 9. NavMenu - Génération automatique des URLs

Le composant NavMenu génère automatiquement les bonnes URLs selon :

- **type** : post, archive
- **entity_type** : Post, Product, Event
- **category_id** : ID de la catégorie (optionnel)

```php
// src/Interface/FrontEnd/Component/NavMenu.php
if ($type === 'archive') {
    $entityType = $item->getEntityType();

    if ($categoryId) {
        $category = Category::find($categoryId);
        $url = "/{$entityType}s/" . $category->getSlug();
    } else {
        $url = "/archive?entity={$entityType}";
    }
}
```

## Avantages de cette architecture

✅ **Évolutif** : Ajoutez autant d'entités que nécessaire (Product, Event, Project, etc.)
✅ **DRY** : Une seule vue `archive.blade.php` pour tous les types
✅ **Flexible** : Chaque entité peut avoir sa propre vue si nécessaire
✅ **Centralisé** : Le gestionnaire de menu gère tous les types d'entités
✅ **Type-safe** : Utilisation de match() expressions pour la sécurité des types

## Migration nécessaire

Avant d'utiliser entity_type, exécuter :

```bash
php minor migrate
```

Cela ajoute la colonne `entity_type` à la table `menuitems`.
