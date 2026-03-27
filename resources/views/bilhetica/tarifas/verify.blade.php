<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGTP - Verificação 2FA</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #07203f; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; color: white; }
        .card { background: white; border-radius: 32px; padding: 40px; width: 100%; max-width: 400px; color: #0d2e5e; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .icon { font-size: 3rem; color: #f39c12; margin-bottom: 20px; }
        h2 { margin-bottom: 10px; font-weight: 800; }
        p { color: #64748b; font-size: 0.9rem; margin-bottom: 30px; }
        .code-input { width: 100%; height: 50px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1.5rem; text-align: center; letter-spacing: 10px; font-weight: bold; color: #0d2e5e; margin-bottom: 20px; transition: border-color 0.3s; }
        .code-input:focus { outline: none; border-color: #f39c12; }
        .btn { width: 100%; padding: 14px; background: #0d2e5e; color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: transform 0.2s; }
        .btn:hover { transform: translateY(-2px); background: #1a4d8c; }
        .error { color: #dc2626; font-size: 0.8rem; margin-top: -15px; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon"><i class="fas fa-shield-halved"></i></div>
        <h2>Acesso Protegido</h2>
        <p>Insira o código de 6 dígitos gerado pelo seu aplicativo autenticador.</p>

        @if($errors->any())
            <span class="error">{{ $errors->first() }}</span>
        @endif

        <form method="POST" action="{{ route('2fa.verify') }}">
            @csrf
            <input type="text" name="code" class="code-input" maxlength="6" placeholder="000000" autofocus required inputmode="numeric">
            
            <button type="submit" class="btn">
                Verificar Identidade
            </button>
        </form>

        <div style="margin-top: 20px;">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #94a3b8; text-decoration: none; font-size: 0.8rem;">
                Cancelar e Sair
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</body>
</html>
