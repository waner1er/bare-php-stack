@php
    $entityLabel = $entityLabel ?? 'articles';
    $entityType = $entityType ?? 'Post';
@endphp
@component('components.layout', [
    'pageTitle' => ($currentCategory ? $currentCategory->getName() : 'Archive') . ' - Mon Portfolio',
])
    <div class="container archive-page">
        <div class="page-header">
            <h1 class="page-title">
                @if ($currentCategory)
                    {{ $currentCategory->getName() }}
                @else
                    Tous les {{ $entityLabel }}
                @endif
            </h1>
        </div>

        @if (count($categories) > 0)
            <div class="categories-filter">
                <a href="/posts" class="category-btn {{ !$currentCategory ? 'category-btn--active' : '' }}">
                    Toutes les catégories
                </a>
                @foreach ($categories as $category)
                    <a href="/posts/{{ $category->getSlug() }}"
                        class="category-btn {{ $currentCategory && $currentCategory->getId() === $category->getId() ? 'category-btn--active' : '' }}">
                        {{ $category->getName() }} ({{ $category->getPostCount() }})
                    </a>
                @endforeach
            </div>
        @endif

        @if (count($posts) > 0)
            <div class="posts-list">
                @foreach ($posts as $post)
                    <article class="post-item">
                        <div class="post-item__header">
                            <div>
                                <h2 class="post-item__title">{{ $post->getTitle() }}</h2>
                                @if ($post->getCategory())
                                    <span class="post-item__category">{{ $post->getCategory()->getName() }}</span>
                                @endif
                            </div>
                        </div>
                        <p class="post-item__excerpt">
                            {{ substr(strip_tags($post->getContent()), 0, 250) }}...
                        </p>
                        <div class="post-item__meta">
                            <a href="/posts/{{ $post->getSlug() }}" class="btn">Lire la suite →</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="no-posts">
                <h3>Aucun {{ rtrim($entityLabel, 's') }} trouvé</h3>
                <p>Il n'y a pas encore de {{ $entityLabel }} dans cette catégorie.</p>
            </div>
        @endif
    </div>
@endcomponent
