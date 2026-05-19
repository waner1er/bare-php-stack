@component('components.layout', ['pageTitle' => 'Inscription - Mon Portfolio'])
    <div class="container auth-page">
        <h1 class="page-title">Inscription</h1>

        @if (isset($error))
            <div class="alert alert--error">{{ $error }}</div>
        @endif

        <form class="form" method="POST" action="/register">
            {!! csrf_field() !!}
            <div class="form__group">
                <label class="form__label" for="first_name">Prénom</label>
                <input class="form__input" type="text" name="first_name" id="first_name" required>
            </div>

            <div class="form__group">
                <label class="form__label" for="last_name">Nom</label>
                <input class="form__input" type="text" name="last_name" id="last_name" required>
            </div>

            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input class="form__input" type="email" name="email" id="email" required>
            </div>

            <div class="form__group">
                <label class="form__label" for="password">Mot de passe</label>
                <input class="form__input" type="password" name="password" id="password" required>
            </div>

            <div class="form__group">
                <label class="form__label" for="password_confirmation">Confirmer le mot de passe</label>
                <input class="form__input" type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn--block">S'inscrire</button>
        </form>

        <p class="auth-page__footer">
            Déjà un compte ? <a href="/login">Se connecter</a>
        </p>
    </div>
@endcomponent
