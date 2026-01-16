@php
    use App\Infrastructure\Session\Session;
@endphp
@component('components.bo-layout', ['pageTitle' => $title])
    @php $isDev = getenv('APP_ENV') === 'development'; @endphp
    @if ($isDev)
        {{-- Load Vite dev server modules for HMR --}}
        <script type="module" src="http://localhost:3000/src/Interface/Admin/Assets/js/admin.js"></script>
        <script type="module" src="http://localhost:3000/src/Interface/Admin/Assets/js/crud.js"></script>
    @else
        <link rel="stylesheet" href="/dist/css/admin-crud-style.css">
    @endif

    <div class="admin-page">
        <div class="admin-page__header">
            <h2 class="admin-page__title">{{ $title }}</h2>
            @if ($mode === 'list')
                <a href="/admin/crud/{{ $resource }}?action=create" class="admin-btn admin-btn--primary">
                    ➕ Ajouter {{ $singularTitle }}
                </a>
            @else
                <a href="/admin/crud/{{ $resource }}" class="admin-btn admin-btn--secondary">
                    ← Retour à la liste
                </a>
            @endif
        </div>

        @if (Session::has('success'))
            <div class="admin-alert admin-alert--success">
                {{ Session::getFlash('success') }}
            </div>
        @endif

        @if (Session::has('error'))
            <div class="admin-alert admin-alert--error">
                {{ Session::getFlash('error') }}
                @if (Session::has('validation_errors'))
                    <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                        @foreach (Session::getFlash('validation_errors') as $field => $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        @if ($mode === 'list')
            {!! $content !!}
        @else
            <div class="crud-form-wrapper">
                <form method="POST" action="/admin/crud/{{ $resource }}/store" class="crud-form">
                    {!! csrf_field() !!}
                    @if ($id)
                        <input type="hidden" name="id" value="{{ $id }}">
                    @endif
                    {!! $content !!}
                </form>
            </div>
        @endif
    </div>

    @if (!$isDev)
        <script type="module" src="/dist/js/admin-crud.js"></script>
    @endif
@endcomponent
