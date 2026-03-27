@extends('layouts.app')

@section('title', 'Colaboradores - SGTP')
@section('page-title', 'Gestão de Colaboradores')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-users"></i> Colaboradores</span>
        @can('gerir_colaboradores')
        <a href="{{ route('colaboradores.create') }}" class="btn-accent">
            <i class="fas fa-plus"></i> Novo Colaborador
        </a>
        @endcan
    </div>
    
    <!-- Filtros -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" id="searchInput" placeholder="Buscar por nome, BI ou telefone..." class="form-control" style="width: 300px;">
        <select id="tipoFilter" class="form-control" style="width: 150px;">
            <option value="">Todos os tipos</option>
            <option value="MOTORISTA">Motoristas</option>
            <option value="COBRADOR">Cobradores</option>
            <option value="FISCAL">Fiscais</option>
        </select>
        <select id="statusFilter" class="form-control" style="width: 150px;">
            <option value="">Todos os status</option>
            <option value="ATIVO">Ativos</option>
            <option value="INATIVO">Inativos</option>
        </select>
        <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="colaboradoresTable">
            <thead>
                 <tr>
                    <th>Nome</th>
                    <th>BI</th>
                    <th>Função</th>
                    <th>Telefone</th>
                    <th>Carta</th>
                    <th>Status</th>
                    <th>Ações</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($colaboradores as $colaborador)
                 <tr>
                    <td><strong>{{ $colaborador->nome_completo }}</strong></td>
                    <td>{{ $colaborador->bi }}</td>
                    <td>
                        @php
                            $tipoIcon = match($colaborador->tipo) {
                                'MOTORISTA' => 'fa-user-check',
                                'COBRADOR' => 'fa-user-tie',
                                'FISCAL' => 'fa-user-shield',
                                default => 'fa-user'
                            };
                        @endphp
                        <i class="fas {{ $tipoIcon }}"></i> {{ $colaborador->tipo }}
                    </td>
                    <td>{{ $colaborador->telefone }}</td>
                    <td>
                        @if($colaborador->tipo == 'MOTORISTA')
                            {{ $colaborador->categoria_carta }} - 
                            @if($colaborador->carta_validade < now())
                                <span class="status-badge status-inativo">Expirada</span>
                            @elseif($colaborador->carta_validade < now()->addDays(30))
                                <span class="status-badge status-em-andamento">Vence em breve</span>
                            @else
                                <span class="status-badge status-ativo">Válida</span>
                            @endif
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        @if($colaborador->data_demissao)
                            <span class="status-badge status-inativo">Inativo</span>
                        @else
                            <span class="status-badge status-ativo">Ativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('colaboradores.show', $colaborador) }}" class="btn-sm" style="background: var(--info);" title="Ver detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('gerir_colaboradores')
                        <a href="{{ route('colaboradores.edit', $colaborador) }}" class="btn-sm" style="background: var(--warning);" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if(!$colaborador->data_demissao)
                        <form action="{{ route('colaboradores.destroy', $colaborador) }}" method="POST" style="display: inline;" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm" style="background: var(--danger);" title="Desativar" onclick="return confirm('Tem certeza que deseja desativar este colaborador?')">
                                <i class="fas fa-user-slash"></i>
                            </button>
                        </form>
                        @endif
                        @endcan
                    </td>
                 </tr>
                @empty
                 <tr>
                    <td colspan="7" style="text-align: center;">Nenhum colaborador cadastrado.</td>
                 </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $colaboradores->links() }}
    </div>
</div>

<!-- Estatísticas -->
<div class="row-cards" style="margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-line"></i> Estatísticas</span>
        </div>
        <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-title">Total</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #d1fae5;">
                <div class="stat-value">{{ $stats['motoristas'] ?? 0 }}</div>
                <div class="stat-title">Motoristas</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #fff3e0;">
                <div class="stat-value">{{ $stats['cobradores'] ?? 0 }}</div>
                <div class="stat-title">Cobradores</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #e0f2fe;">
                <div class="stat-value">{{ $stats['fiscais'] ?? 0 }}</div>
                <div class="stat-title">Fiscais</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('filterBtn')?.addEventListener('click', function() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const tipo = document.getElementById('tipoFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        document.querySelectorAll('#colaboradoresTable tbody tr').forEach(row => {
            let show = true;
            
            if(search) {
                const text = row.textContent.toLowerCase();
                if(!text.includes(search)) show = false;
            }
            
            if(tipo && show) {
                const tipoCell = row.cells[2]?.textContent;
                if(!tipoCell.includes(tipo)) show = false;
            }
            
            if(status && show) {
                const statusCell = row.cells[5]?.textContent.trim();
                if(status === 'ATIVO' && statusCell !== 'Ativo') show = false;
                if(status === 'INATIVO' && statusCell !== 'Inativo') show = false;
            }
            
            row.style.display = show ? '' : 'none';
        });
    });
</script>
@endpush