@php
    use App\Infrastructure\Session\Session;
@endphp
@component('components.bo-layout')
    <div style="margin-bottom: 20px;">
        <h2>Gestion du menu</h2>
        <p>Cochez les articles à afficher dans le menu et définissez leur ordre d'apparition.</p>
    </div>

    @if (isset($success))
        <div style="padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px;">
            {{ $success }}
        </div>
    @endif

    @if (isset($error))
        <div style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
            {{ $error }}
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 12px; text-align: left;">Titre</th>
                <th style="padding: 12px; text-align: left;">Slug</th>
                <th style="padding: 12px; text-align: center;">Dans le menu</th>
                <th style="padding: 12px; text-align: center;">Position</th>
                <th style="padding: 12px; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $post->getTitle() }}</td>
                    <td style="padding: 12px;"><code>{{ $post->getSlug() }}</code></td>
                    <td style="padding: 12px; text-align: center;">
                        <form method="POST" action="/admin/posts/{{ $post->getId() }}/toggle-menu"
                            style="display: inline;">
                            {!! csrf_field() !!}
                            <input type="checkbox" {{ $post->getIsInMenu() ? 'checked' : '' }} onchange="this.form.submit()"
                                style="cursor: pointer; width: 20px; height: 20px;">
                        </form>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if ($post->getIsInMenu())
                            <div style="display: inline-flex; align-items: center; gap: 5px;">
                                <form method="POST" action="/admin/posts/{{ $post->getId() }}/move-up"
                                    style="display: inline;">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn"
                                        style="padding: 5px 10px; font-size: 12px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px;">
                                        ▲
                                    </button>
                                </form>
                                <span
                                    style="min-width: 30px; text-align: center; font-weight: bold;">{{ $menuPositions[$post->getId()] ?? '-' }}</span>
                                <form method="POST" action="/admin/posts/{{ $post->getId() }}/move-down"
                                    style="display: inline;">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn"
                                        style="padding: 5px 10px; font-size: 12px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px;">
                                        ▼
                                    </button>
                                </form>
                            </div>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="/posts/{{ $post->getSlug() }}" target="_blank" class="btn btn-primary"
                            style="font-size: 12px;">
                            👁️ Voir
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($posts) === 0)
        <div style="padding: 40px; text-align: center; color: #6c757d;">
            Aucun article disponible
        </div>
    @endif
@endcomponent
