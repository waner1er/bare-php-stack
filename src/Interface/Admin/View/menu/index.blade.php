@component('components.bo-layout', ['pageTitle' => 'Gestion du menu'])
    <div class="admin-page">
        <div class="admin-page__header">
            <h2 class="admin-page__title">Gestion du menu</h2>
            <p class="admin-page__description">Ajoutez des articles et catégories au menu de navigation.</p>
        </div>

        @if ($success ?? false)
            <div class="admin-alert admin-alert--success">{{ $success }}</div>
        @endif

        @if ($error ?? false)
            <div class="admin-alert admin-alert--error">{{ $error }}</div>
        @endif

        <div class="menu-section">
            <h2 class="menu-section__title">Ajouter un élément au menu</h2>
            <form method="POST" action="/admin/menu/add" id="menuForm" class="admin-form"
                data-used-slugs='[
                    @foreach ($menuItems as $item)
                        "{{ $item->getSlug() }}"{{ !$loop->last ? ',' : '' }} @endforeach
                ]'>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="admin-form__group">
                    <label for="label" class="admin-form__label">Label :</label>
                    <input type="text" id="label" name="label" class="admin-form__input" required>
                </div>

                <div class="admin-form__group">
                    <label for="slug" class="admin-form__label">Slug :</label>
                    <input type="text" id="slug" name="slug" class="admin-form__input" required>
                    <small id="slug-error" class="admin-form__error" style="display: none;"></small>
                    <small id="slug-warning" class="admin-form__warning" style="display: none;"></small>
                </div>

                <div class="admin-form__group">
                    <label for="type" class="admin-form__label">Type :</label>
                    <select id="type" name="type" class="admin-form__select" onchange="toggleCategorySelect()">
                        <option value="post">Article individuel</option>
                        <option value="archive">Liste d'articles</option>
                    </select>
                    <small class="admin-form__hint">Articles individuels ou liste complète/par catégorie.</small>
                </div>

                <div class="admin-form__group" id="categoryGroup" style="display: none;">
                    <label for="category_id" class="admin-form__label">Catégorie (optionnel) :</label>
                    <select id="category_id" name="category_id" class="admin-form__select"
                        onchange="updateLabelAndSlugFromCategory()">
                        <option value="">Toutes les catégories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->getId() }}" data-name="{{ $category->getName() }}"
                                data-slug="{{ $category->getSlug() }}">{{ $category->getName() }}</option>
                        @endforeach
                    </select>
                    <small class="admin-form__hint">Laissez vide pour afficher tous les articles.</small>
                </div>

                <button type="submit" id="submitBtn" class="admin-btn admin-btn--primary">Ajouter</button>
            </form>

            <div id="postsSection">
                <h3 class="menu-section__subtitle">Articles disponibles (cliquez pour ajouter)</h3>
                @if (count($availableSlugs) > 0)
                    <div class="slug-grid">
                        @foreach ($availableSlugs as $slug)
                            <div class="slug-card post-card" data-type="post"
                                onclick="fillForm('{{ addslashes($slug['title']) }}', '{{ $slug['slug'] }}', '{{ $slug['type'] }}')">
                                <div class="slug-card__title">{{ $slug['title'] }}</div>
                                <div class="slug-card__info">
                                    <span class="slug-card__path">/{{ $slug['slug'] }}</span>
                                    <span class="slug-card__type badge-{{ $slug['type'] }}">{{ $slug['type'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="no-slugs">Tous les articles sont déjà dans le menu.</p>
                @endif
            </div>

            <div id="categoriesSection" style="display: none;">
                <h3 class="menu-section__subtitle">Catégories disponibles (cliquez pour ajouter)</h3>
                @if (count($categoryCards) > 0)
                    <div class="slug-grid">
                        <div class="slug-card archive-card" data-type="archive" onclick="fillFormForAllPosts()">
                            <div class="slug-card__title">Tous les articles</div>
                            <div class="slug-card__info">
                                <span class="slug-card__path">/posts</span>
                                <span class="slug-card__type badge-archive">archive</span>
                            </div>
                        </div>
                        @foreach ($categoryCards as $cat)
                            <div class="slug-card archive-card" data-type="archive"
                                onclick="fillFormForCategory('{{ addslashes($cat['name']) }}', '{{ $cat['slug'] }}', {{ $cat['id'] }})">
                                <div class="slug-card__title">{{ $cat['name'] }}</div>
                                <div class="slug-card__info">
                                    <span class="slug-card__path">/posts/{{ $cat['slug'] }}</span>
                                    <span class="slug-card__type badge-archive">archive</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="no-slugs">Aucune catégorie disponible.</p>
                @endif
            </div>
        </div>

        <div class="menu-section">
            <h2 class="menu-section__title">Éléments du menu</h2>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead class="admin-table__head">
                        <tr class="admin-table__row">
                            <th class="admin-table__header">Position</th>
                            <th class="admin-table__header">Label</th>
                            <th class="admin-table__header">Slug</th>
                            <th class="admin-table__header">Type</th>
                            <th class="admin-table__header admin-table__header--center">Visible</th>
                            <th class="admin-table__header admin-table__header--center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="admin-table__body">
                        @foreach ($menuItems as $item)
                            <tr class="admin-table__row">
                                <td class="admin-table__cell">{{ $item->getPosition() }}</td>
                                <td class="admin-table__cell">{{ $item->getLabel() }}</td>
                                <td class="admin-table__cell">
                                    <code class="admin-code">{{ $item->getSlug() }}</code>
                                </td>
                                <td class="admin-table__cell">
                                    <span
                                        class="slug-card__type badge-{{ $item->getType() }}">{{ $item->getType() }}</span>
                                </td>
                                <td class="admin-table__cell admin-table__cell--center">
                                    <form method="POST" action="/admin/menu/{{ $item->getId() }}/toggle-visibility"
                                        class="admin-form--inline">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="checkbox" class="admin-checkbox"
                                            {{ $item->getIsVisible() ? 'checked' : '' }} onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td class="admin-table__cell admin-table__cell--center">
                                    <form method="POST" action="/admin/menu/{{ $item->getId() }}/move-up"
                                        class="admin-form--inline">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit"
                                            class="admin-btn admin-btn--small admin-btn--secondary">▲</button>
                                    </form>
                                    <form method="POST" action="/admin/menu/{{ $item->getId() }}/move-down"
                                        class="admin-form--inline">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit"
                                            class="admin-btn admin-btn--small admin-btn--secondary">▼</button>
                                    </form>
                                    <form method="POST" action="/admin/menu/{{ $item->getId() }}/delete"
                                        class="admin-form--inline"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit"
                                            class="admin-btn admin-btn--small admin-btn--danger">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
