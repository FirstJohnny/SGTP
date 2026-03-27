@extends('layouts.app')

@section('title', 'Escalas - SGTP')
@section('page-title', 'Gestão de Escalas')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-calendar-alt"></i> Escalas de Trabalho</span>
        @can('gerir_operacoes')
        <a href="{{ route('escalas.create') }}" class="btn-accent">
            <i class="fas fa-plus"></i> Nova Escala
        </a>
        @endcan
    </div>
    
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="date" id="dataFilter" class="form-control" style="width: 150px;" value="{{ date('Y-m-d') }}">
        <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
        <a href="{{ route('escalas.index') }}" class="btn" style="background: var(--gray-500);"><i class="fas fa-undo"></i> Hoje</a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Data</th>
                    <th>Veículo</th>
                    <th>Motorista</th>
                    <th>Cobrador</th>
                    <th>Rota</th>
                    <th>Horário</th>
                    <th>Status</th>
                    <th>Ações</th>
                 </tr>
            </thead>
            <tbody id="escalasTable">
                <tr><td colspan="8" style="text-align: center;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function carregarEscalas() {
        const data = document.getElementById('dataFilter').value;
        fetch(`/api/escalas/diarias?data=${data}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('escalasTable');
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align: center;">Nenhuma escala para esta data</td></tr>';
                    return;
                }
                tbody.innerHTML = data.map(escala => `
                    <tr>
                        <td>${escala.data}</td>
                        <td>${escala.veiculo?.placa || 'N/A'}</td>
                        <td>${escala.motorista?.nome_completo || 'N/A'}</td>
                        <td>${escala.cobrador?.nome_completo || '--'}</td>
                        <td>${escala.rota?.nome || 'N/A'}</td>
                        <td>${escala.hora_inicio} - ${escala.hora_fim}</td>
                        <td><span class="status-badge status-${escala.status === 'FINALIZADA' ? 'ativo' : 'em-andamento'}">${escala.status}</span></td>
                        <td>
                            <a href="/escalas/${escala.id}" class="btn-sm" style="background: var(--info);"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                `).join('');
            });
    }
    
    document.getElementById('filterBtn').addEventListener('click', carregarEscalas);
    carregarEscalas();
</script>
@endpush
@endsection