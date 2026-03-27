
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de Autenticação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            padding: 20px;
            background: #1a4d8c;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            background: #f4f4f4;
            border-radius: 5px;
            letter-spacing: 5px;
            color: #1a4d8c;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>SGTP - Sistema de Gestão de Transportes Públicos</h2>
        </div>

        <div style="padding: 20px;">
            <h3>Olá,</h3>
            <p>Você solicitou um código de autenticação de dois fatores para acessar o sistema SGTP.</p>
            <p>Utilize o código abaixo para completar seu login:</p>

            <div class="code">
                {{ $code }}
            </div>

            <p><strong>Este código expira em 5 minutos.</strong></p>
            <p>Se você não solicitou este código, por favor ignore este e-mail ou entre em contato com o suporte.</p>
        </div>

        <div class="footer">
            <p>SGTP - Sistema de Gestão de Transportes Públicos</p>
            <p>Este é um e-mail automático, por favor não responda.</p>
        </div>
    </div>
</body>
</html>
