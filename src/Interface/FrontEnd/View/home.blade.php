@component('components.layout', ['pageTitle' => 'Accueil - Mon Portfolio'])
    <div class="hero">
        <h1 class="hero__title">Bienvenue sur mon Portfolio</h1>
        <p class="hero__subtitle">Découvrez mes projets et réalisations</p>
    </div>

    @if (count($recentPosts) > 0)
        <div class="container">
            <h2 class="section-title">Derniers Articles</h2>
            <div class="posts-grid">
                @foreach ($recentPosts as $post)
                    <div class="post-card">
                        <div class="post-card__content">
                            @if ($post->getCategory())
                                <span class="post-card__category">{{ $post->getCategory()->getName() }}</span>
                            @endif
                            <h3 class="post-card__title">{{ $post->getTitle() }}</h3>
                            <p class="post-card__excerpt">{{ substr(strip_tags($post->getContent()), 0, 150) }}...</p>
                            <a href="/posts/{{ $post->getSlug() }}" class="btn">Lire la suite</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="cta-section">
        <h2 class="cta-section__title">Vous avez un projet ?</h2>
        <p class="cta-section__text">N'hésitez pas à me contacter pour discuter de vos besoins</p>
        <a href="/contact" class="btn btn--large">Me contacter</a>
    </div>
@endcomponent
