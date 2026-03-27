@extends('layouts.app')

@section('title', 'Detalhes do Log - SGTP')
@section('page-title', 'Detalhes do Registro de Auditoria')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-info-circle"></i> Informações do Log</span>
        <a href="{{ route('admin.auditoria.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <div class="row-cards">
        <div><strong>ID:</strong> {{ $log->id }}</div>
        <div><strong>Data/Hora:</strong> {{ \Carbon\Carbon::parse($log->timestamp)->format('d/m/Y H:i:s') }}</div>
        <div><strong>Usuário:</strong> {{ $log->usuario->name ?? 'N/A' }} ({{ $log->usuario->email ?? 'N/A' }})</div>
        <div><strong>Ação:</strong> <span class="status-badge status-info">{{ $log->acao }}</span></div>
        <div><strong>Entidade:</strong> {{ $log->entidade }}</div>
        <div><strong>ID Entidade:</strong> {{ $log->entidade_id ?? 'N/A' }}</div>
        <div><strong>IP Address:</strong> {{ $log->ip_address }}</div>
        <div><strong>User Agent:</strong> {{ $log->user_agent ?? 'N/A' }}</div>
    </div>
    
    @if($log->dados_anteriores)
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <span><i class="fas fa-history"></i> Dados Anteriores</span>
        </div>
        <pre style="background: #f4f4f4; padding: 16px; border-radius: 12px; overflow-x: auto;">{{ json_encode($log->dados_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    @endif
    
    @if($log->dados_novos)
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <span><i class="fas fa-edit"></i> Dados Novos</span>
        </div>
        <pre style="background: #f4f4f4; padding: 16px; border-radius: 12px; overflow-x: auto;">{{ json_encode($log->dados_novos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    @endif
</div>
@endsection