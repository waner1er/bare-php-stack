@component('components.layout')
    <div style="max-width: 400px; margin: 50px auto;">
        <h1>Inscription</h1>

        @if (isset($error))
            <div style="padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 20px;">
                {{ $error }}
            </div>
        @endif

        <form method="POST" action="/register">
            {!! csrf_field() !!}
            <div style="margin-bottom: 15px;">
                <label for="first_name">Prénom</label>
                <input type="text" name="first_name" id="first_name" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="last_name">Nom</label>
                <input type="text" name="last_name" id="last_name" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    style="width: 100%; padding: 8px;">
            </div>

            <button type="submit"
                style="width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer;">
                S'inscrire
            </button>
        </form>

        <p style="margin-top: 20px; text-align: center;">
            Déjà un compte ? <a href="/login">Se connecter</a>
        </p>
    </div>
@endcomponent
