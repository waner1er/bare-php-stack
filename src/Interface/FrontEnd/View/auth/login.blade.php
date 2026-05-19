@component('components.layout', ['pageTitle' => 'Connexion - Mon Portfolio'])
    <div class="container auth-page">
        <h1 class="page-title">Connexion</h1>

        @if (isset($error))
            <div class="alert alert--error">{{ $error }}</div>
        @endif

        <form class="form" method="POST" action="/login">
            {!! csrf_field() !!}
            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input class="form__input" type="email" name="email" id="email" required>
            </div>

            <div class="form__group">
                <label class="form__label" for="password">Mot de passe</label>
                <input class="form__input" type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="btn btn--block">Se connecter</button>
        </form>

        <p class="auth-page__footer">
            Pas de compte ? <a href="/register">S'inscrire</a>
        </p>
    </div>
@endcomponent
