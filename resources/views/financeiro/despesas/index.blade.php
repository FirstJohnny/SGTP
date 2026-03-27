@extends('layouts.app')

@section('title', 'Despesas - SGTP')
@section('page-title', 'Gestão de Despesas')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-money-bill-wave"></i> Despesas</span>
        @can('gerir_financeiro')
        <button onclick="abrirModal()" class="btn-accent">
            <i class="fas fa-plus"></i> Nova Despesa
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
                    <th>Valor</th>
                    <th>Aprovado</th>
                    <th>Ações</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($despesas as $despesa)
                 <tr>
                    <td>{{ \Carbon\Carbon::parse($despesa->data)->format('d/m/Y') }}</td>
                    <td>{{ $despesa->veiculo->placa ?? 'N/A' }}</td>
                    <td>{{ $despesa->tipo }}</td>
                    <td>{{ Str::limit($despesa->descricao, 40) }}</td>
                    <td><strong>Kz {{ number_format($despesa->valor, 2, ',', '.') }}</strong></td>
                    <td>
                        @if($despesa->aprovado)
                            <span class="status-badge status-ativo">Aprovada</span>
                        @else
                            <span class="status-badge status-pendente">Pendente</span>
                        @endif
                    </td>
                    <td>
                        @if(!$despesa->aprovado && can('gerir_financeiro'))
                        <form action="{{ route('despesas.aprovar', $despesa) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-sm" style="background: var(--success);">Aprovar</button>
                        </form>
                        @endif
                    </td>
                 </tr>
                @empty
                 <tr><td colspan="7">Nenhuma despesa registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $despesas->links() }}
    </div>
</div>

<!-- Modal Nova Despesa -->
<div id="despesaModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 24px; padding: 24px; width: 500px; max-width: 90%;">
        <h3>Nova Despesa</h3>
        <form method="POST" action="{{ route('despesas.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Veículo</label>
                <select name="veiculo_id" class="form-control">
                    <option value="">Selecione (opcional)</option>
                    @foreach($veiculos as $veiculo)
                    <option value="{{ $veiculo->id }}">{{ $veiculo->placa }} - {{ $veiculo->marca }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tipo <span style="color: red;">*</span></label>
                <select name="tipo" class="form-control" required>
                    <option value="MANUTENCAO">Manutenção</option>
                    <option value="COMBUSTIVEL">Combustível</option>
                    <option value="SEGURO">Seguro</option>
                    <option value="MULTA">Multa</option>
                    <option value="SALARIO">Salário</option>
                    <option value="OUTRO">Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Valor <span style="color: red;">*</span></label>
                <input type="number" step="0.01" name="valor" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Data <span style="color: red;">*</span></label>
                <input type="date" name="data" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Comprovativo</label>
                <input type="file" name="documento" class="form-control">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="fecharModal()" class="btn" style="background: var(--gray-500);">Cancelar</button>
                <button type="submit" class="btn-accent">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModal() {
        document.getElementById('despesaModal').style.display = 'flex';
    }
    function fecharModal() {
        document.getElementById('despesaModal').style.display = 'none';
    }
</script>
@endsection