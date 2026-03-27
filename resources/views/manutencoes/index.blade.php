@extends('layouts.app')

@section('title', 'Manutenções - SGTP')
@section('page-title', 'Gestão de Manutenções')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-tools"></i> Manutenções</span>
        @can('gerir_frota')
        <button onclick="abrirModal()" class="btn-accent">
            <i class="fas fa-plus"></i> Agendar Manutenção
        </button>
        @endcan
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Data</th>
                    <th>Veículo</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Custo</th>
                    <th>Status</th>
                    <th>Ações</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($manutencoes as $manutencao)
                 <tr>
                    <td>{{ \Carbon\Carbon::parse($manutencao->data_agendamento)->format('d/m/Y') }}</td>
                    <td>{{ $manutencao->veiculo->placa ?? 'N/A' }}</td>
                    <td>{{ $manutencao->tipo }}</td>
                    <td>{{ Str::limit($manutencao->descricao, 50) }}</td>
                    <td>Kz {{ number_format($manutencao->custo_total, 2, ',', '.') }}</td>
                    <td><span class="status-badge status-{{ $manutencao->status == 'CONCLUIDA' ? 'ativo' : 'em-andamento' }}">{{ $manutencao->status }}</span></td>
                    <td>
                        <a href="{{ route('manutencoes.show', $manutencao) }}" class="btn-sm" style="background: var(--info);"><i class="fas fa-eye"></i></a>
                    </td>
                 </tr>
                @empty
                 <tr><td colspan="7" style="text-align: center;">Nenhuma manutenção registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $manutencoes->links() }}
    </div>
</div>
@endsection