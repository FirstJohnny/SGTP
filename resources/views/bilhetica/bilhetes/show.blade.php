{{-- resources/views/bilhetica/bilhetes/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Bilhete - SGTP')
@section('page-title', 'Detalhes do Bilhete')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-ticket-alt"></i> Informações do Bilhete</span>
        <div>
            <a href="{{ route('bilhetes.qr', $bilhete) }}" class="btn-sm" style="background: var(--info);" target="_blank">
                <i class="fas fa-qrcode"></i> Ver QR Code
            </a>
            <a href="{{ route('dashboard') }}" class="btn-sm" style="background: var(--gray-500);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div>
            <div class="form-group">
                <p><strong>Código de Barras:</strong></p>
                <div style="background: #f4f4f4; padding: 15px; border-radius: 12px; text-align: center; font-family: monospace; font-size: 1.2rem; letter-spacing: 2px;">
                    {{ $bilhete->codigo_barras }}
                </div>
            </div>
        </div>
        <div>
            <p><strong>Rota:</strong> {{ $bilhete->tarifa->rota->nome ?? 'N/A' }}</p>
            <p><strong>Tipo Passageiro:</strong> {{ $bilhete->tarifa->tipo_passageiro }}</p>
            <p><strong>Valor Pago:</strong> <strong style="color: var(--accent); font-size: 1.3rem;">Kz {{ number_format($bilhete->valor_pago, 2, ',', '.') }}</strong></p>
            <p><strong>Data Venda:</strong> {{ $bilhete->data_venda->format('d/m/Y H:i') }}</p>
            <p><strong>Validade:</strong> {{ $bilhete->data_validade->format('d/m/Y') }}</p>
            <p><strong>Forma Pagamento:</strong> {{ $bilhete->forma_pagamento }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ $bilhete->status == 'VALIDO' ? 'ativo' : 'inativo' }}">
                    {{ $bilhete->status }}
                </span>
            </p>
            @if($bilhete->validacao)
            <p><strong>Validado em:</strong> {{ $bilhete->validacao->timestamp->format('d/m/Y H:i') }}</p>
            <p><strong>Veículo:</strong> {{ $bilhete->validacao->veiculo->placa ?? 'N/A' }}</p>
            @endif
        </div>
    </div>
</div>

<div class="card" style="margin-top: 24px; text-align: center;">
    <div class="card-header">
        <span><i class="fas fa-qrcode"></i> QR Code de Validação</span>
    </div>
    <div id="qrcode" style="display: inline-block; margin: 20px auto;"></div>
    <p>Apresente este QR Code ao fiscal ou utilize o validador do veículo.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs2-fix/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: '{{ $bilhete->codigo_barras }}',
        width: 200,
        height: 200,
        colorDark: "#1a4d8c",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
@endsection