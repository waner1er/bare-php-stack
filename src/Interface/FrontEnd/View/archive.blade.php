@php
    use App\Interface\FrontEnd\Component\NavMenu;
    $navMenu = new NavMenu();
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentCategory ? $currentCategory->getName() : 'Archive' }} - Mon Portfolio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-title {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .categories-filter {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 50px;
        }

        .category-btn {
            padding: 10px 25px;
            background: white;
            color: #333;
            text-decoration: none;
            border-radius: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .category-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .category-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .posts-list {
            display: grid;
            gap: 30px;
        }

        .post-item {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .post-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .post-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 10px;
        }

        .post-category {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .post-excerpt {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .read-more {
            display: inline-block;
            padding: 10px 25px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .read-more:hover {
            background: #5568d3;
        }

        .no-posts {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-posts h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    {!! $navMenu->render() !!}

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                @if ($currentCategory)
                    {{ $currentCategory->getName() }}
                @else
                    Tous les articles
                @endif
            </h1>
        </div>

        <div class="categories-filter">
            <a href="/archive" class="category-btn {{ !$currentCategory ? 'active' : '' }}">
                Tous les articles
            </a>
            @foreach ($categories as $category)
                <a href="/archive?category={{ $category->getSlug() }}"
                    class="category-btn {{ $currentCategory && $currentCategory->getId() === $category->getId() ? 'active' : '' }}">
                    {{ $category->getName() }} ({{ $category->getPostCount() }})
                </a>
            @endforeach
        </div>

        @if (count($posts) > 0)
            <div class="posts-list">
                @foreach ($posts as $post)
                    <article class="post-item">
                        <div class="post-header">
                            <div>
                                <h2 class="post-title">{{ $post->getTitle() }}</h2>
                                @if ($post->getCategory())
                                    <span class="post-category">{{ $post->getCategory()->getName() }}</span>
                                @endif
                            </div>
                        </div>

                        <p class="post-excerpt">
                            {{ substr(strip_tags($post->getContent()), 0, 250) }}...
                        </p>

                        <div class="post-footer">
                            <a href="/posts/{{ $post->getSlug() }}" class="read-more">Lire l'article</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="no-posts">
                <h3>Aucun article trouvé</h3>
                <p>Il n'y a pas encore d'articles dans cette catégorie.</p>
            </div>
        @endif
    </div>
</body>

</html>
