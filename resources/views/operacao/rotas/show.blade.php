@extends('layouts.app')

@section('title', 'Detalhes da Rota - SGTP')
@section('page-title', 'Detalhes da Rota: {{ $rota->nome }}')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-map-marked-alt"></i> Informações da Rota</span>
        <div>
            <a href="{{ route('rotas.edit', $rota) }}" class="btn-sm" style="background: var(--warning);">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('rotas.index') }}" class="btn-sm" style="background: var(--gray-500);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div>
            <p><strong>Código:</strong> {{ $rota->codigo }}</p>
            <p><strong>Tipo:</strong> {{ $rota->tipo }}</p>
            <p><strong>Distância Total:</strong> {{ number_format($rota->distancia_total, 1) }} km</p>
            <p><strong>Tempo Estimado:</strong> {{ $rota->tempo_estimado }} minutos</p>
        </div>
        <div>
            <p><strong>Status:</strong> 
                @if($rota->ativa)
                    <span class="status-badge status-ativo">Ativa</span>
                @else
                    <span class="status-badge status-inativo">Inativa</span>
                @endif
            </p>
            <p><strong>Empresa Responsável:</strong> {{ $rota->empresa_responsavel ?? 'N/A' }}</p>
            <p><strong>Data Cadastro:</strong> {{ $rota->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
    
    <div class="form-group">
        <p><strong>Descrição:</strong></p>
        <p>{{ $rota->descricao ?? 'Nenhuma descrição cadastrada.' }}</p>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-clock"></i> Horários da Rota</span>
        <a href="{{ route('rotas.horarios.index', $rota) }}" class="btn-sm" style="background: var(--accent);">
            <i class="fas fa-plus"></i> Gerenciar Horários
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Hora Partida</th>
                    <th>Hora Chegada</th>
                    <th>Dias</th>
                    <th>Tipo</th>
                    <th>Status</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($rota->horarios->take(5) as $horario)
                 <tr>
                    <td>{{ $horario->hora_partida }}</td>
                    <td>{{ $horario->hora_chegada }}</td>
                    <td>{{ $horario->dias_semana }}</td>
                    <td>{{ $horario->tipo_dia }}</td>
                    <td>
                        @if($horario->ativo)
                            <span class="status-badge status-ativo">Ativo</span>
                        @else
                            <span class="status-badge status-inativo">Inativo</span>
                        @endif
                    </td>
                 </tr>
                @empty
                 <tr><td colspan="5">Nenhum horário cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-tags"></i> Tarifas</span>
        <a href="{{ route('tarifas.index') }}?rota_id={{ $rota->id }}" class="btn-sm" style="background: var(--accent);">
            <i class="fas fa-plus"></i> Ver Tarifas
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Tipo Passageiro</th>
                    <th>Valor</th>
                    <th>Vigência</th>
                    <th>Status</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($rota->tarifas->where('ativa', true)->take(3) as $tarifa)
                 <tr>
                    <td>{{ $tarifa->tipo_passageiro }}</td>
                    <td><strong>Kz {{ number_format($tarifa->valor, 2, ',', '.') }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($tarifa->data_inicio)->format('d/m/Y') }}
                        @if($tarifa->data_fim) até {{ \Carbon\Carbon::parse($tarifa->data_fim)->format('d/m/Y') }} @endif
                    </td>
                    <td>
                        @if($tarifa->ativa && $tarifa->isVigente())
                            <span class="status-badge status-ativo">Vigente</span>
                        @else
                            <span class="status-badge status-inativo">Inativa</span>
                        @endif
                    </td>
                 </tr>
                @empty
                 <tr><td colspan="4">Nenhuma tarifa cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection