@extends('layouts.app')

@section('title', 'Detalhes do Bilhete - SGTP')
@section('page-title', 'Consulta de Bilhete')

@section('content')
<div class="row-cards">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-ticket-alt"></i> Informações do Bilhete</span>
            <a href="{{ url()->previous() }}" class="btn-sm" style="background: var(--gray-500);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        
        <div style="display: flex; gap: 32px; flex-wrap: wrap; align-items: center; justify-content: center; padding: 20px;">
            <div style="text-align: center; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="background: #f8fafc; padding: 10px; border-radius: 12px; margin-bottom: 15px;">
                    <!-- Assume a biblioteca simple-qrcode no Laravel -->
                    {!! QrCode::size(200)->generate($bilhete->codigo_unico) !!}
                </div>
                <strong style="font-size: 1.2rem; color: var(--navy);">{{ $bilhete->codigo_unico }}</strong>
                <div style="margin-top: 10px;">
                    @php
                        $statusClass = match($bilhete->status) {
                            'VENDIDO' => 'status-pendente',
                            'VALIDADO' => 'status-ativo',
                            'EXPIRADO' => 'status-inativo',
                            default => 'status-danger'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}" style="font-size: 1rem; padding: 8px 20px;">
                        {{ $bilhete->status }}
                    </span>
                </div>
            </div>

            <div style="flex: 1; min-width: 300px;">
                <table class="data-table">
                    <tr>
                        <th style="width: 150px;">Rota:</th>
                        <td>{{ $bilhete->rota->nome ?? 'N/A' }} ({{ $bilhete->rota->codigo ?? '--' }})</td>
                    </tr>
                    <tr>
                        <th>Tipo Passageiro:</th>
                        <td>{{ $bilhete->tipo_passageiro }}</td>
                    </tr>
                    <tr>
                        <th>Valor Pago:</th>
                        <td><strong>Kz {{ number_format($bilhete->valor, 2, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Data da Venda:</th>
                        <td>{{ \Carbon\Carbon::parse($bilhete->data_venda)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Vendedor:</th>
                        <td>{{ $bilhete->vendedor->name ?? 'Sistema' }}</td>
                    </tr>
                    <tr>
                        <th>Validade:</th>
                        <td>{{ \Carbon\Carbon::parse($bilhete->data_validade)->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($bilhete->status == 'VENDIDO')
        <div style="margin-top: 24px; padding: 20px; border-top: 1px solid var(--gray-200); text-align: right;">
            <form action="{{ route('bilhetes.validar', $bilhete) }}" method="POST">
                @csrf
                <button type="submit" class="btn-accent" onclick="return confirm('Confirmar validação manual?')">
                    <i class="fas fa-check-circle"></i> Validar Manualmente
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection