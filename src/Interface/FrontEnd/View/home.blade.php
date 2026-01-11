@php
    use App\Interface\FrontEnd\Component\NavMenu;
    $navMenu = new NavMenu();
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Mon Portfolio</title>
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
        }

        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            color: #333;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .post-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
        }

        .post-card-content {
            padding: 25px;
        }

        .post-category {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }

        .post-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }

        .post-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #5568d3;
        }

        .cta-section {
            background: #f8f9fa;
            padding: 80px 20px;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .cta-section p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .btn-large {
            padding: 15px 40px;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    {!! $navMenu->render() !!}

    <div class="hero">
        <h1>Bienvenue sur mon Portfolio</h1>
        <p>Découvrez mes projets et réalisations</p>
    </div>

    @if (count($recentPosts) > 0)
        <div class="container">
            <h2 class="section-title">Derniers Articles</h2>
            <div class="posts-grid">
                @foreach ($recentPosts as $post)
                    <div class="post-card">
                        <div class="post-card-content">
                            @if ($post->getCategory())
                                <span class="post-category">{{ $post->getCategory()->getName() }}</span>
                            @endif
                            <h3>{{ $post->getTitle() }}</h3>
                            <p>{{ substr(strip_tags($post->getContent()), 0, 150) }}...</p>
                            <a href="/posts/{{ $post->getSlug() }}" class="btn">Lire la suite</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="cta-section">
        <h2>Vous avez un projet ?</h2>
        <p>N'hésitez pas à me contacter pour discuter de vos besoins</p>
        <a href="/contact" class="btn btn-large">Me contacter</a>
    </div>
</body>

</html>
