@extends('layouts.app')

@section('title', 'Gestão de Frota - SGTP')
@section('page-title', 'Gestão de Frota')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-truck"></i> Veículos Cadastrados</span>
        @can('gerir_frota')
        <a href="{{ route('frota.create') }}" class="btn-accent">
            <i class="fas fa-plus"></i> Novo Veículo
        </a>
        @endcan
    </div>
    
    <!-- Filtros -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" id="searchInput" placeholder="Buscar por placa, marca ou modelo..." class="form-control" style="width: 300px;">
        <select id="statusFilter" class="form-control" style="width: 150px;">
            <option value="">Todos os status</option>
            <option value="ATIVO">Ativo</option>
            <option value="MANUTENCAO">Manutenção</option>
            <option value="INATIVO">Inativo</option>
        </select>
        <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
        <button id="resetBtn" class="btn"><i class="fas fa-undo"></i> Limpar</button>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="veiculosTable">
            <thead>
                 <tr>
                    <th>Placa</th>
                    <th>Modelo/Marca</th>
                    <th>Ano</th>
                    <th>KM Atual</th>
                    <th>Status</th>
                    <th>Seguro</th>
                    <th>Ações</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($veiculos as $veiculo)
                 <tr>
                    <td><strong>{{ $veiculo->placa }}</strong></td>
                    <td>{{ $veiculo->modelo }} / {{ $veiculo->marca }}</td>
                    <td>{{ $veiculo->ano_fabricado }}</td>
                    <td>{{ number_format($veiculo->km_atual, 0, ',', '.') }} km</td>
                    <td>
                        @php
                            $statusClass = match($veiculo->status) {
                                'ATIVO' => 'status-ativo',
                                'MANUTENCAO' => 'status-em-andamento',
                                default => 'status-inativo'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $veiculo->status }}
                        </span>
                    </td>
                    <td>
                        @if($veiculo->seguro_validade < now())
                            <span class="status-badge status-inativo">Expirado</span>
                        @elseif($veiculo->seguro_validade < now()->addDays(30))
                            <span class="status-badge status-em-andamento">Vence em breve</span>
                        @else
                            <span class="status-badge status-ativo">Válido</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('frota.show', $veiculo) }}" class="btn-sm" style="background: var(--info);" title="Ver detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('gerir_frota')
                        <a href="{{ route('frota.edit', $veiculo) }}" class="btn-sm" style="background: var(--warning);" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('frota.destroy', $veiculo) }}" method="POST" style="display: inline;" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm" style="background: var(--danger);" title="Remover" onclick="return confirm('Tem certeza que deseja remover este veículo?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                 </tr>
                @empty
                 <tr>
                    <td colspan="7" style="text-align: center;">Nenhum veículo cadastrado.</td>
                 </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $veiculos->links() }}
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row-cards" style="margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-pie"></i> Resumo da Frota</span>
        </div>
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); gap: 16px;">
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-title">Total de Veículos</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #d1fae5;">
                <div class="stat-value">{{ $stats['ativos'] ?? 0 }}</div>
                <div class="stat-title">Ativos</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #fed7aa;">
                <div class="stat-value">{{ $stats['manutencao'] ?? 0 }}</div>
                <div class="stat-title">Em Manutenção</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-calendar-alt"></i> Documentos a Vencer</span>
        </div>
        @if(count($documentosVencendo ?? []) > 0)
        <ul style="list-style: none; padding: 0;">
            @foreach($documentosVencendo as $doc)
            <li style="padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                <i class="fas fa-file-invoice" style="color: var(--warning);"></i>
                {{ $doc->veiculo->placa }} - {{ $doc->tipo }} vence em {{ \Carbon\Carbon::parse($doc->data_validade)->format('d/m/Y') }}
            </li>
            @endforeach
        </ul>
        @else
        <p>Todos os documentos estão em dia! <i class="fas fa-check-circle" style="color: var(--success);"></i></p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('filterBtn')?.addEventListener('click', function() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        
        document.querySelectorAll('#veiculosTable tbody tr').forEach(row => {
            let show = true;
            
            if(search) {
                const text = row.textContent.toLowerCase();
                if(!text.includes(search)) show = false;
            }
            
            if(status && show) {
                const statusCell = row.cells[4]?.textContent.trim();
                if(!statusCell.includes(status)) show = false;
            }
            
            row.style.display = show ? '' : 'none';
        });
    });
    
    document.getElementById('resetBtn')?.addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.querySelectorAll('#veiculosTable tbody tr').forEach(row => {
            row.style.display = '';
        });
    });
</script>
@endpush