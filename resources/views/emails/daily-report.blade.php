<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório Diário - SGTP</title>
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
        .content {
            padding: 20px;
        }
        .stats {
            background: #f4f7fc;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
        .badge {
            background: #f39c12;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>SGTP - Relatório Diário</h2>
            <p>{{ $dados['data'] }}</p>
        </div>
        
        <div class="content">
            <p>Olá,</p>
            <p>Segue o resumo das operações do dia <strong>{{ $dados['data'] }}</strong>:</p>
            
            <div class="stats">
                <div class="stat-item">
                    <span>💰 Receitas totais:</span>
                    <strong>Kz {{ number_format($dados['receitas'], 2) }}</strong>
                </div>
                <div class="stat-item">
                    <span>💸 Despesas totais:</span>
                    <strong>Kz {{ number_format($dados['despesas'], 2) }}</strong>
                </div>
                <div class="stat-item">
                    <span>📊 Resultado do dia:</span>
                    <strong>Kz {{ number_format($dados['receitas'] - $dados['despesas'], 2) }}</strong>
                </div>
                <div class="stat-item">
                    <span>🚌 Escalas realizadas:</span>
                    <strong>{{ $dados['escalas_realizadas'] }}</strong>
                </div>
                <div class="stat-item">
                    <span>👥 Passageiros transportados:</span>
                    <strong>{{ number_format($dados['passageiros']) }}</strong>
                </div>
            </div>
            
            <p style="margin-top: 20px;">
                Este é um relatório automático. Para mais detalhes, acesse o sistema SGTP.
            </p>
        </div>
        
        <div class="footer">
            <p>SGTP - Sistema de Gestão de Transportes Públicos</p>
            <p>Este e-mail foi enviado automaticamente. Por favor, não responda.</p>
        </div>
    </div>
</body>
</html>