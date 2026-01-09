@component('components.layout')
    @foreach ($posts as $post)
        <h2>{{ $post->getTitle() }}</h2>
        <p>{{ $post->getContent() }}</p>
    @endforeach
@endcomponent
