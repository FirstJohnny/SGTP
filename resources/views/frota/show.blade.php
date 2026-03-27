@extends('layouts.app')

@section('title', 'Detalhes do Veículo - SGTP')
@section('page-title', 'Detalhes do Veículo: {{ $veiculo->placa }}')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-truck"></i> Informações do Veículo</span>
        <div>
            <a href="{{ route('frota.edit', $veiculo) }}" class="btn-sm" style="background: var(--warning);">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('frota.index') }}" class="btn-sm" style="background: var(--gray-500);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    
    <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div>
            <p><strong>Placa:</strong> {{ $veiculo->placa }}</p>
            <p><strong>Chassi:</strong> {{ $veiculo->chassi }}</p>
            <p><strong>Marca:</strong> {{ $veiculo->marca }}</p>
            <p><strong>Modelo:</strong> {{ $veiculo->modelo }}</p>
            <p><strong>Ano:</strong> {{ $veiculo->ano_fabricado }}</p>
            <p><strong>Cor:</strong> {{ $veiculo->cor }}</p>
        </div>
        <div>
            <p><strong>Lotação:</strong> {{ $veiculo->lotacao }} passageiros</p>
            <p><strong>Combustível:</strong> {{ $veiculo->tipo_combustivel }}</p>
            <p><strong>Consumo Médio:</strong> {{ $veiculo->consumo_medio ?? 'N/A' }} km/l</p>
            <p><strong>KM Atual:</strong> {{ number_format($veiculo->km_atual, 0, ',', '.') }} km</p>
            <p><strong>Data Aquisição:</strong> {{ \Carbon\Carbon::parse($veiculo->data_aquisicao)->format('d/m/Y') }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ strtolower($veiculo->status) == 'ativo' ? 'ativo' : 'inativo' }}">
                    {{ $veiculo->status }}
                </span>
            </p>
        </div>
        <div>
            <p><strong>Seguro:</strong> 
                @if($veiculo->seguro_validade < now())
                    <span class="status-badge status-inativo">Expirado em {{ \Carbon\Carbon::parse($veiculo->seguro_validade)->format('d/m/Y') }}</span>
                @elseif($veiculo->seguro_validade < now()->addDays(30))
                    <span class="status-badge status-em-andamento">Vence em {{ \Carbon\Carbon::parse($veiculo->seguro_validade)->format('d/m/Y') }}</span>
                @else
                    <span class="status-badge status-ativo">Válido até {{ \Carbon\Carbon::parse($veiculo->seguro_validade)->format('d/m/Y') }}</span>
                @endif
            </p>
            <p><strong>Última Inspeção:</strong> {{ $veiculo->ultima_inspecao ? \Carbon\Carbon::parse($veiculo->ultima_inspecao)->format('d/m/Y') : 'N/A' }}</p>
            <p><strong>Próxima Inspeção:</strong> {{ $veiculo->proxima_inspecao ? \Carbon\Carbon::parse($veiculo->proxima_inspecao)->format('d/m/Y') : 'N/A' }}</p>
        </div>
    </div>
    
    @if($veiculo->observacoes)
    <div class="form-group">
        <p><strong>Observações:</strong></p>
        <p>{{ $veiculo->observacoes }}</p>
    </div>
    @endif
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-history"></i> Histórico de Manutenções</span>
        <a href="{{ route('manutencoes.index') }}?veiculo_id={{ $veiculo->id }}" class="btn-sm" style="background: var(--accent);">
            <i class="fas fa-plus"></i> Ver Todas
        </a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Custo</th>
                    <th>Status</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($veiculo->manutencoes->take(5) as $manutencao)
                 <tr>
                    <td>{{ \Carbon\Carbon::parse($manutencao->data_agendamento)->format('d/m/Y') }}</td>
                    <td>{{ $manutencao->tipo }}</td>
                    <td>{{ Str::limit($manutencao->descricao, 50) }}</td>
                    <td>Kz {{ number_format($manutencao->custo_total, 2, ',', '.') }}</td>
                    <td><span class="status-badge status-{{ $manutencao->status == 'CONCLUIDA' ? 'ativo' : 'em-andamento' }}">{{ $manutencao->status }}</span></td>
                 </tr>
                @empty
                 <tr><td colspan="5">Nenhuma manutenção registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-gas-pump"></i> Histórico de Abastecimentos</span>
        <a href="{{ route('abastecimentos.index') }}?veiculo_id={{ $veiculo->id }}" class="btn-sm" style="background: var(--accent);">
            <i class="fas fa-plus"></i> Ver Todos
        </a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Data</th>
                    <th>Odômetro</th>
                    <th>Litros</th>
                    <th>Valor Total</th>
                    <th>Posto</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($veiculo->abastecimentos->take(5) as $abastecimento)
                 <tr>
                    <td>{{ \Carbon\Carbon::parse($abastecimento->data)->format('d/m/Y') }}</td>
                    <td>{{ number_format($abastecimento->odometro, 0, ',', '.') }} km</td>
                    <td>{{ number_format($abastecimento->litros, 2, ',', '.') }} L</td>
                    <td>Kz {{ number_format($abastecimento->valor_total, 2, ',', '.') }}</td>
                    <td>{{ $abastecimento->posto }}</td>
                 </tr>
                @empty
                 <tr><td colspan="5">Nenhum abastecimento registrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection