<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGTP - Recuperar Senha</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a4d8c 0%, #0e3a6b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 32px;
            padding: 48px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-area {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1a4d8c, #f39c12);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .logo-icon i {
            font-size: 3rem;
            color: white;
        }

        .logo-area h1 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1a4d8c, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #f39c12;
            box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a4d8c, #0e3a6b);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: #f39c12;
            text-decoration: none;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .alert-info {
            background: #e0f2fe;
            color: #075985;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fas fa-bus"></i>
                </div>
                <h1>SGTP</h1>
                <p style="color: #64748b; margin-top: 8px;">Recuperar senha</p>
            </div>

            @if(session('status'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <div class="alert-info">
                <i class="fas fa-info-circle"></i> Funcionalidade em desenvolvimento
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Digite seu e-mail" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Enviar link de recuperação</button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Voltar para o login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
