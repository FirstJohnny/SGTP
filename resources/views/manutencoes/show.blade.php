@extends('layouts.app')

@section('title', 'Detalhes da Manutenção - SGTP')
@section('page-title', 'Detalhes da Manutenção')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-tools"></i> Informações da Manutenção</span>
        <a href="{{ route('manutencoes.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div>
            <p><strong>Veículo:</strong> {{ $manutencao->veiculo->placa }} - {{ $manutencao->veiculo->marca }} {{ $manutencao->veiculo->modelo }}</p>
            <p><strong>Tipo:</strong> {{ $manutencao->tipo }}</p>
            <p><strong>Data Agendamento:</strong> {{ \Carbon\Carbon::parse($manutencao->data_agendamento)->format('d/m/Y') }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ $manutencao->status == 'CONCLUIDA' ? 'ativo' : 'em-andamento' }}">
                    {{ $manutencao->status }}
                </span>
            </p>
        </div>
        <div>
            <p><strong>Data Início:</strong> {{ $manutencao->data_inicio ? \Carbon\Carbon::parse($manutencao->data_inicio)->format('d/m/Y H:i') : '--' }}</p>
            <p><strong>Data Fim:</strong> {{ $manutencao->data_fim ? \Carbon\Carbon::parse($manutencao->data_fim)->format('d/m/Y H:i') : '--' }}</p>
            <p><strong>Oficina:</strong> {{ $manutencao->oficina }}</p>
            <p><strong>Custo Total:</strong> <strong style="color: var(--accent);">Kz {{ number_format($manutencao->custo_total, 2, ',', '.') }}</strong></p>
        </div>
    </div>
    
    <div class="form-group">
        <p><strong>Descrição:</strong></p>
        <p>{{ $manutencao->descricao }}</p>
    </div>
    
    @if($manutencao->observacoes)
    <div class="form-group">
        <p><strong>Observações:</strong></p>
        <p>{{ $manutencao->observacoes }}</p>
    </div>
    @endif
</div>

@if($manutencao->pecasTrocadas->count() > 0)
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-microchip"></i> Peças Trocadas</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Peça</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                    <th>Garantia</th>
                  </thead>
            <tbody>
                @foreach($manutencao->pecasTrocadas as $peca)
                  <tr>
                    <td>{{ $peca->nome_peca }}</td>
                    <td>{{ $peca->quantidade }}</td>
                    <td>Kz {{ number_format($peca->preco_unitario, 2, ',', '.') }}</td>
                    <td>Kz {{ number_format($peca->quantidade * $peca->preco_unitario, 2, ',', '.') }}</td>
                    <td>{{ $peca->garantia_meses ? $peca->garantia_meses . ' meses' : '--' }}</td>
                  </tr>
                @endforeach
            </tbody>
          </table>
    </div>
</div>
@endif

@if($manutencao->status != 'CONCLUIDA' && can('gerir_frota'))
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-check-circle"></i> Registrar Execução</span>
    </div>
    <form action="{{ route('manutencoes.executar', $manutencao) }}" method="POST">
        @csrf
        <div class="row-cards">
            <div class="form-group">
                <label class="form-label">Data Início</label>
                <input type="datetime-local" name="data_inicio" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Data Fim</label>
                <input type="datetime-local" name="data_fim" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Custo Peças (Kz)</label>
                <input type="number" step="0.01" name="custo_pecas" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Custo Mão de Obra (Kz)</label>
                <input type="number" step="0.01" name="custo_mao_obra" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn-accent">Concluir Manutenção</button>
    </form>
</div>
@endif
@endsection