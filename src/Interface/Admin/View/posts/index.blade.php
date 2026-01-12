@php
    use App\Infrastructure\Session\Session;
@endphp
@component('components.bo-layout', ['pageTitle' => 'Gestion du menu'])
    <div class="admin-page">
        <div class="admin-page__header">
            <h2 class="admin-page__title">Gestion du menu</h2>
            <p class="admin-page__description">Cochez les articles à afficher dans le menu et définissez leur ordre
                d'apparition.</p>
        </div>

        @if (isset($success))
            <div class="admin-alert admin-alert--success">
                {{ $success }}
            </div>
        @endif

        @if (isset($error))
            <div class="admin-alert admin-alert--error">
                {{ $error }}
            </div>
        @endif

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead class="admin-table__head">
                    <tr class="admin-table__row">
                        <th class="admin-table__header">Titre</th>
                        <th class="admin-table__header">Slug</th>
                        <th class="admin-table__header admin-table__header--center">Dans le menu</th>
                        <th class="admin-table__header admin-table__header--center">Position</th>
                        <th class="admin-table__header admin-table__header--center">Actions</th>
                    </tr>
                </thead>
                <tbody class="admin-table__body">
                    @foreach ($posts as $post)
                        <tr class="admin-table__row">
                            <td class="admin-table__cell">{{ $post->getTitle() }}</td>
                            <td class="admin-table__cell">
                                <code class="admin-code">{{ $post->getSlug() }}</code>
                            </td>
                            <td class="admin-table__cell admin-table__cell--center">
                                <form method="POST" action="/admin/posts/{{ $post->getId() }}/toggle-menu"
                                    class="admin-form--inline">
                                    {!! csrf_field() !!}
                                    <input type="checkbox" class="admin-checkbox"
                                        {{ $post->getIsInMenu() ? 'checked' : '' }} onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="admin-table__cell admin-table__cell--center">
                                @if ($post->getIsInMenu())
                                    <div class="admin-position-controls">
                                        <form method="POST" action="/admin/posts/{{ $post->getId() }}/move-up"
                                            class="admin-form--inline">
                                            {!! csrf_field() !!}
                                            <button type="submit" class="admin-btn admin-btn--small admin-btn--secondary">
                                                ▲
                                            </button>
                                        </form>
                                        <span
                                            class="admin-position-controls__value">{{ $menuPositions[$post->getId()] ?? '-' }}</span>
                                        <form method="POST" action="/admin/posts/{{ $post->getId() }}/move-down"
                                            class="admin-form--inline">
                                            {!! csrf_field() !!}
                                            <button type="submit" class="admin-btn admin-btn--small admin-btn--secondary">
                                                ▼
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="admin-text--muted">-</span>
                                @endif
                            </td>
                            <td class="admin-table__cell admin-table__cell--center">
                                <a href="/posts/{{ $post->getSlug() }}" target="_blank"
                                    class="admin-btn admin-btn--small admin-btn--primary">
                                    👁️ Voir
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (count($posts) === 0)
            <div class="admin-empty-state">
                Aucun article disponible
            </div>
        @endif
    </div>
@endcomponent
