@extends('layouts.app')

@section('title', 'Detalhes do Colaborador - SGTP')
@section('page-title', 'Detalhes do Colaborador: {{ $colaborador->nome_completo }}')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-user-circle"></i> Informações Pessoais</span>
        <div>
            <a href="{{ route('colaboradores.edit', $colaborador) }}" class="btn-sm" style="background: var(--warning);">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('colaboradores.index') }}" class="btn-sm" style="background: var(--gray-500);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div>
            @if($colaborador->foto_url)
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="{{ $colaborador->foto_url }}" alt="Foto" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent);">
            </div>
            @else
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 150px; height: 150px; background: var(--gray-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-user-circle" style="font-size: 80px; color: var(--gray-400);"></i>
                </div>
            </div>
            @endif
            
            <div class="form-group">
                <p><strong>Nome Completo:</strong> {{ $colaborador->nome_completo }}</p>
                <p><strong>Tipo:</strong> 
                    @php
                        $tipoIcon = match($colaborador->tipo) {
                            'MOTORISTA' => 'fa-user-check',
                            'COBRADOR' => 'fa-user-tie',
                            'FISCAL' => 'fa-user-shield',
                            default => 'fa-user'
                        };
                    @endphp
                    <i class="fas {{ $tipoIcon }}"></i> {{ $colaborador->tipo }}
                </p>
                <p><strong>BI:</strong> {{ $colaborador->bi }}</p>
                <p><strong>Telefone:</strong> {{ $colaborador->telefone }}</p>
                <p><strong>E-mail:</strong> {{ $colaborador->email ?? 'Não informado' }}</p>
            </div>
        </div>
        
        <div>
            <div class="form-group">
                <p><strong>Data Contratação:</strong> {{ \Carbon\Carbon::parse($colaborador->data_contratacao)->format('d/m/Y') }}</p>
                @if($colaborador->data_demissao)
                <p><strong>Data Demissão:</strong> {{ \Carbon\Carbon::parse($colaborador->data_demissao)->format('d/m/Y') }}</p>
                @endif
                <p><strong>Status:</strong> 
                    @if($colaborador->data_demissao)
                        <span class="status-badge status-inativo">Inativo</span>
                    @else
                        <span class="status-badge status-ativo">Ativo</span>
                    @endif
                </p>
                <p><strong>Salário Base:</strong> <strong style="color: var(--accent);">Kz {{ number_format($colaborador->salario_base, 2, ',', '.') }}</strong></p>
                <p><strong>Nº Segurança Social:</strong> {{ $colaborador->numero_seguranca_social }}</p>
                <p><strong>Contato Emergência:</strong> {{ $colaborador->emergencia_contato }}</p>
            </div>
        </div>
    </div>
    
    @if($colaborador->tipo == 'MOTORISTA')
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <span><i class="fas fa-id-card"></i> Dados da Carteira de Condução</span>
        </div>
        <div class="row-cards">
            <div class="form-group">
                <p><strong>Nº Carta:</strong> {{ $colaborador->numero_carta ?? 'Não informado' }}</p>
                <p><strong>Categoria:</strong> {{ $colaborador->categoria_carta ?? 'Não informado' }}</p>
                <p><strong>Validade:</strong> 
                    @if($colaborador->carta_validade)
                        {{ \Carbon\Carbon::parse($colaborador->carta_validade)->format('d/m/Y') }}
                        @if($colaborador->carta_validade < now())
                            <span class="status-badge status-inativo">Expirada</span>
                        @elseif($colaborador->carta_validade < now()->addDays(30))
                            <span class="status-badge status-em-andamento">Vence em breve</span>
                        @else
                            <span class="status-badge status-ativo">Válida</span>
                        @endif
                    @else
                        Não informado
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif
</div>

@if($escalas->count() > 0)
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-calendar-alt"></i> Escalas Realizadas</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Data</th>
                    <th>Veículo</th>
                    <th>Rota</th>
                    <th>Horário</th>
                    <th>KM Percorrido</th>
                    <th>Status</th>
                 </tr>
            </thead>
            <tbody>
                @foreach($escalas as $escala)
                 <tr>
                    <td>{{ \Carbon\Carbon::parse($escala->data)->format('d/m/Y') }}</td>
                    <td>{{ $escala->veiculo->placa ?? 'N/A' }}</td>
                    <td>{{ $escala->rota->nome ?? 'N/A' }}</td>
                    <td>{{ $escala->hora_inicio }} - {{ $escala->hora_fim }}</td>
                    <td>{{ $escala->kmPercorrido() ?? '--' }} km</td>
                    <td>
                        <span class="status-badge status-{{ $escala->status == 'FINALIZADA' ? 'ativo' : 'em-andamento' }}">
                            {{ $escala->status }}
                        </span>
                    </td>
                 </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($ocorrencias->count() > 0)
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-exclamation-triangle"></i> Ocorrências Registradas</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Data</th>
                    <th>Veículo</th>
                    <th>Tipo</th>
                    <th>Gravidade</th>
                    <th>Status</th>
                    <th>Ações</th>
                 </tr>
            </thead>
            <tbody>
                @foreach($ocorrencias as $ocorrencia)
                 <tr>
                    <td>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y H:i') }}</td>
                    <td>{{ $ocorrencia->veiculo->placa ?? 'N/A' }}</td>
                    <td>{{ $ocorrencia->tipo }}</td>
                    <td>
                        <span class="status-badge status-{{ $ocorrencia->gravidade == 'LEVE' ? 'ativo' : 'em-andamento' }}">
                            {{ $ocorrencia->gravidade }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $ocorrencia->status == 'RESOLVIDA' ? 'ativo' : 'pendente' }}">
                            {{ $ocorrencia->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('ocorrencias.show', $ocorrencia) }}" class="btn-sm" style="background: var(--info);">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                 </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($colaborador->user_id)
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-laptop"></i> Acesso ao Sistema</span>
    </div>
    <div class="row-cards">
        <p><strong>Usuário:</strong> {{ $colaborador->usuario->name ?? 'N/A' }}</p>
        <p><strong>E-mail:</strong> {{ $colaborador->usuario->email ?? 'N/A' }}</p>
        <p><strong>Último Acesso:</strong> {{ $colaborador->usuario->ultimo_acesso ? \Carbon\Carbon::parse($colaborador->usuario->ultimo_acesso)->format('d/m/Y H:i') : 'Nunca' }}</p>
    </div>
</div>
@endif
@endsection