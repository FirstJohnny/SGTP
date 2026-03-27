@extends('layouts.app')

@section('title', 'Rotas - SGTP')
@section('page-title', 'Gestão de Rotas')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-map-marked-alt"></i> Rotas Cadastradas</span>
        @can('gerir_operacoes')
        <a href="{{ route('rotas.create') }}" class="btn-accent">
            <i class="fas fa-plus"></i> Nova Rota
        </a>
        @endcan
    </div>
    
    <!-- Filtros -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" id="searchInput" placeholder="Buscar por nome ou código..." class="form-control" style="width: 250px;">
        <select id="tipoFilter" class="form-control" style="width: 180px;">
            <option value="">Todos os tipos</option>
            <option value="URBANA">Urbana</option>
            <option value="INTERMUNICIPAL">Intermunicipal</option>
            <option value="RODOVIARIA">Rodoviária</option>
            <option value="ESCOLAR">Escolar</option>
        </select>
        <select id="statusFilter" class="form-control" style="width: 120px;">
            <option value="">Status</option>
            <option value="1">Ativas</option>
            <option value="0">Inativas</option>
        </select>
        <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
        <button id="resetBtn" class="btn"><i class="fas fa-undo"></i> Limpar</button>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="rotasTable">
            <thead>
                  <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Distância</th>
                    <th>Tempo Estimado</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
            </thead>
            <tbody>
                @forelse($rotas as $rota)
                  <tr>
                    <td><strong>{{ $rota->codigo }}</strong></td>
                    <td>{{ $rota->nome }}</td>
                    <td>
                        @php
                            $tipoIcon = match($rota->tipo) {
                                'URBANA' => 'fa-city',
                                'INTERMUNICIPAL' => 'fa-road',
                                'RODOVIARIA' => 'fa-truck',
                                'ESCOLAR' => 'fa-school',
                                default => 'fa-map'
                            };
                        @endphp
                        <i class="fas {{ $tipoIcon }}"></i> {{ $rota->tipo }}
                    </td>
                    <td>{{ number_format($rota->distancia_total, 1) }} km</td>
                    <td>{{ $rota->tempo_estimado }} min</td>
                    <td>
                        @if($rota->ativa)
                            <span class="status-badge status-ativo">Ativa</span>
                        @else
                            <span class="status-badge status-inativo">Inativa</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('rotas.show', $rota) }}" class="btn-sm" style="background: var(--info);" title="Ver detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('gerir_operacoes')
                        <a href="{{ route('rotas.edit', $rota) }}" class="btn-sm" style="background: var(--warning);" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('horarios.index', ['rota' => $rota->id]) }}" class="btn-sm" style="background: var(--accent);" title="Horários">
                            <i class="fas fa-clock"></i>
                        </a>
                        @endcan
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" style="text-align: center;">Nenhuma rota cadastrada.</td>
                  </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $rotas->links() }}
    </div>
</div>

<!-- Estatísticas -->
<div class="row-cards" style="margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-line"></i> Estatísticas de Rotas</span>
        </div>
        <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); gap: 16px;">
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-title">Total</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #d1fae5;">
                <div class="stat-value">{{ $stats['ativas'] ?? 0 }}</div>
                <div class="stat-title">Ativas</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #e0f2fe;">
                <div class="stat-value">{{ $stats['urbanas'] ?? 0 }}</div>
                <div class="stat-title">Urbanas</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #fed7aa;">
                <div class="stat-value">{{ number_format($stats['distancia_total'] ?? 0, 1) }} km</div>
                <div class="stat-title">Total km</div>
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
        
        document.querySelectorAll('#rotasTable tbody tr').forEach(row => {
            let show = true;
            
            if(search && !row.textContent.toLowerCase().includes(search)) show = false;
            if(tipo && !row.cells[2]?.textContent.includes(tipo)) show = false;
            if(status !== '' && status === '0' && row.cells[5]?.textContent.includes('Ativa')) show = false;
            if(status !== '' && status === '1' && row.cells[5]?.textContent.includes('Inativa')) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    });
    
    document.getElementById('resetBtn')?.addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('tipoFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.querySelectorAll('#rotasTable tbody tr').forEach(row => {
            row.style.display = '';
        });
    });
</script>
@endpush