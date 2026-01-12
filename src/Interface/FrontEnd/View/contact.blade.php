@component('components.layout', ['pageTitle' => 'Contact - Mon Portfolio'])
    <div class="container contact-page">
        <h1 class="page-title">Contactez-moi</h1>
        <p class="page-subtitle">Une question ? Un projet ? N'hésitez pas à me contacter</p>

        @if (isset($_SESSION['success']))
            <div class="alert alert--success">
                {{ $_SESSION['success'] }}
            </div>
            @php unset($_SESSION['success']); @endphp
        @endif

        @if (isset($_SESSION['error']))
            <div class="alert alert--error">
                {{ $_SESSION['error'] }}
            </div>
            @php unset($_SESSION['error']); @endphp
        @endif

        <form method="POST" action="/contact" class="form">
            {!! csrf_field() !!}

            <div class="form__group">
                <label for="name" class="form__label">Nom complet *</label>
                <input type="text" id="name" name="name" class="form__input" required>
            </div>

            <div class="form__group">
                <label for="email" class="form__label">Email *</label>
                <input type="email" id="email" name="email" class="form__input" required>
            </div>

            <div class="form__group">
                <label for="message" class="form__label">Message *</label>
                <textarea id="message" name="message" class="form__textarea" required></textarea>
            </div>

            <button type="submit" class="btn btn--block">Envoyer le message</button>
        </form>

        <div class="contact-info">
            <h3 class="contact-info__title">Autres moyens de me contacter</h3>
            <div class="contact-info__item">
                <strong>Email :</strong> contact@monportfolio.com
            </div>
            <div class="contact-info__item">
                <strong>LinkedIn :</strong> linkedin.com/in/votreprofil
            </div>
            <div class="contact-info__item">
                <strong>GitHub :</strong> github.com/votreprofil
            </div>
        </div>
    </div>
@endcomponent
