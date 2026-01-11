@component('components.layout')
    @foreach ($posts as $post)
        <div>
            <h2>{{ $post->getTitle() }}</h2>
            <p>{{ $post->getContent() }}</p>
            <a href="/posts/{{ $post->getSlug() }}">Lire la suite</a>
        </div>
    @endforeach
@endcomponent
