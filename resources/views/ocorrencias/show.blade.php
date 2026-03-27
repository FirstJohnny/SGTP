@extends('layouts.app')

@section('title', 'Detalhes da Ocorrência - SGTP')
@section('page-title', 'Detalhes da Ocorrência')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-exclamation-triangle"></i> Informações da Ocorrência</span>
        <div>
            <a href="{{ route('ocorrencias.edit', $ocorrencia) }}" class="btn-sm" style="background: var(--warning);">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('ocorrencias.index') }}" class="btn-sm" style="background: var(--gray-500);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div>
            <p><strong>Data/Hora:</strong> {{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y H:i:s') }}</p>
            <p><strong>Veículo:</strong> {{ $ocorrencia->veiculo->placa ?? 'N/A' }} - {{ $ocorrencia->veiculo->marca ?? '' }} {{ $ocorrencia->veiculo->modelo ?? '' }}</p>
            <p><strong>Tipo:</strong> 
                @php
                    $tipoIcon = match($ocorrencia->tipo) {
                        'ACIDENTE' => 'fa-car-crash',
                        'ATRASO' => 'fa-clock',
                        'FALHA_MECANICA' => 'fa-tools',
                        'ASSALTO' => 'fa-shield-alt',
                        default => 'fa-exclamation-circle'
                    };
                @endphp
                <i class="fas {{ $tipoIcon }}"></i> {{ $ocorrencia->tipo }}
            </p>
            <p><strong>Gravidade:</strong> 
                @php
                    $gravidadeClass = match($ocorrencia->gravidade) {
                        'LEVE' => 'status-pendente',
                        'MEDIA' => 'status-em-andamento',
                        'GRAVE' => 'status-inativo',
                        'CRITICA' => 'status-danger',
                        default => 'status-pendente'
                    };
                @endphp
                <span class="status-badge {{ $gravidadeClass }}">{{ $ocorrencia->gravidade }}</span>
            </p>
        </div>
        <div>
            <p><strong>Colaborador:</strong> {{ $ocorrencia->colaborador->nome_completo ?? 'N/A' }}</p>
            <p><strong>Escala:</strong> {{ $ocorrencia->escala_id ? 'ID: ' . $ocorrencia->escala_id : 'Não associada' }}</p>
            @if($ocorrencia->latitude && $ocorrencia->longitude)
            <p><strong>Localização:</strong> 
                <a href="https://www.google.com/maps?q={{ $ocorrencia->latitude }},{{ $ocorrencia->longitude }}" target="_blank">
                    {{ $ocorrencia->latitude }}, {{ $ocorrencia->longitude }}
                </a>
            </p>
            @endif
            <p><strong>Status:</strong> 
                @php
                    $statusClass = match($ocorrencia->status) {
                        'ABERTA' => 'status-pendente',
                        'EM_ANALISE' => 'status-em-andamento',
                        'RESOLVIDA' => 'status-ativo',
                        'CANCELADA' => 'status-inativo',
                        default => 'status-pendente'
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $ocorrencia->status }}</span>
            </p>
            <p><strong>Sincronizado:</strong> {{ $ocorrencia->sincronizado ? 'Sim' : 'Não' }}</p>
        </div>
    </div>
    
    <div class="form-group">
        <p><strong>Descrição Detalhada:</strong></p>
        <div style="background: var(--gray-100); padding: 16px; border-radius: 12px;">
            {{ $ocorrencia->descricao }}
        </div>
    </div>
    
    @if($ocorrencia->fotos_url)
    <div class="form-group">
        <p><strong>Fotos:</strong></p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @foreach(json_decode($ocorrencia->fotos_url, true) ?? [] as $foto)
            <a href="{{ $foto }}" target="_blank">
                <img src="{{ $foto }}" alt="Foto da ocorrência" style="width: 150px; height: 150px; object-fit: cover; border-radius: 12px;">
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if($ocorrencia->manutencao)
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-tools"></i> Manutenção Associada</span>
    </div>
    <div class="row-cards">
        <p><strong>Tipo:</strong> {{ $ocorrencia->manutencao->tipo }}</p>
        <p><strong>Data Agendamento:</strong> {{ \Carbon\Carbon::parse($ocorrencia->manutencao->data_agendamento)->format('d/m/Y') }}</p>
        <p><strong>Oficina:</strong> {{ $ocorrencia->manutencao->oficina }}</p>
        <p><strong>Status:</strong> {{ $ocorrencia->manutencao->status }}</p>
        <p><strong>Custo Total:</strong> Kz {{ number_format($ocorrencia->manutencao->custo_total, 2, ',', '.') }}</p>
        <a href="{{ route('manutencoes.show', $ocorrencia->manutencao) }}" class="btn-sm" style="background: var(--info);">
            <i class="fas fa-eye"></i> Ver Detalhes
        </a>
    </div>
</div>
@endif

@if($ocorrencia->status != 'RESOLVIDA' && Auth::user()->can('gerir_ocorrencias'))
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-check-circle"></i> Atualizar Status</span>
    </div>
    <form action="{{ route('ocorrencias.update', $ocorrencia) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row-cards">
            <div class="form-group">
                <label class="form-label">Novo Status</label>
                <select name="status" class="form-control" required>
                    <option value="ABERTA" {{ $ocorrencia->status == 'ABERTA' ? 'selected' : '' }}>Aberta</option>
                    <option value="EM_ANALISE" {{ $ocorrencia->status == 'EM_ANALISE' ? 'selected' : '' }}>Em Análise</option>
                    <option value="RESOLVIDA" {{ $ocorrencia->status == 'RESOLVIDA' ? 'selected' : '' }}>Resolvida</option>
                    <option value="CANCELADA" {{ $ocorrencia->status == 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="descricao" class="form-control" rows="3">{{ $ocorrencia->descricao }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn-accent">Atualizar</button>
    </form>
</div>
@endif
@endsection