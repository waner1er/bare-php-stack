@php
    use App\Interface\FrontEnd\Component\NavMenu;
    $navMenu = new NavMenu();
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Mon Portfolio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .page-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .page-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 50px;
        }

        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #5568d3;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .contact-info {
            margin-top: 50px;
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .contact-info h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .contact-info p {
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    {!! $navMenu->render() !!}

    <div class="container">
        <h1 class="page-title">Contactez-moi</h1>
        <p class="page-subtitle">Une question ? Un projet ? N'hésitez pas à me contacter</p>

        @if (isset($_SESSION['success']))
            <div class="alert alert-success">
                {{ $_SESSION['success'] }}
            </div>
            @php unset($_SESSION['success']); @endphp
        @endif

        @if (isset($_SESSION['error']))
            <div class="alert alert-error">
                {{ $_SESSION['error'] }}
            </div>
            @php unset($_SESSION['error']); @endphp
        @endif

        <form method="POST" action="/contact" class="contact-form">
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="name">Nom complet *</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" required></textarea>
            </div>

            <button type="submit" class="btn">Envoyer le message</button>
        </form>

        <div class="contact-info">
            <h3>Autres moyens de me contacter</h3>
            <p><strong>Email :</strong> contact@monportfolio.com</p>
            <p><strong>LinkedIn :</strong> linkedin.com/in/votreprofil</p>
            <p><strong>GitHub :</strong> github.com/votreprofil</p>
        </div>
    </div>
</body>

</html>
