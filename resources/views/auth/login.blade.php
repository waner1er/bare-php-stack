@component('components.layout')
    <div style="max-width: 400px; margin: 50px auto;">
        <h1>Connexion</h1>

        @if (isset($error))
            <div style="padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 20px;">
                {{ $error }}
            </div>
        @endif

        <form method="POST" action="/login">
            <div style="margin-bottom: 15px;">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required style="width: 100%; padding: 8px;">
            </div>

            <button type="submit"
                style="width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer;">
                Se connecter
            </button>
        </form>

        <p style="margin-top: 20px; text-align: center;">
            Pas de compte ? <a href="/register">S'inscrire</a>
        </p>
    </div>
@endcomponent
