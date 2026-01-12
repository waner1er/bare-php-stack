@component('components.layout', ['pageTitle' => $post->getTitle() . ' - Mon Portfolio'])
    <div class="container">
        <article class="post-full">
            <header class="post-full__header">
                <h1 class="post-full__title">{{ $post->getTitle() }}</h1>
                @if ($post->getCategory())
                    <span class="post-card__category">{{ $post->getCategory()->getName() }}</span>
                @endif
            </header>
            <div class="post-full__content">
                {!! $post->getContent() !!}
            </div>
        </article>
        <div class="post-full__actions">
            <a href="/posts" class="btn btn--outline">← Retour aux articles</a>
        </div>
    </div>
@endcomponent
