@extends('layouts.app')

@section('title', 'Relatório de Desempenho de Motoristas - SGTP')
@section('page-title', 'Relatório de Desempenho de Motoristas')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-chart-line"></i> Filtros</span>
    </div>
    
    <form method="GET" action="{{ route('relatorios.desempenho-motoristas') }}" id="filterForm">
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="form-group">
                <label class="form-label">Período Início</label>
                <input type="date" name="periodo_inicio" class="form-control" value="{{ request('periodo_inicio', now()->startOfMonth()->format('Y-m-d')) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Período Fim</label>
                <input type="date" name="periodo_fim" class="form-control" value="{{ request('periodo_fim', now()->format('Y-m-d')) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Motorista</label>
                <select name="motorista_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach($motoristas as $motorista)
                    <option value="{{ $motorista->id }}" {{ request('motorista_id') == $motorista->id ? 'selected' : '' }}>
                        {{ $motorista->nome_completo }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn-accent">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('relatorios.desempenho-motoristas') }}" class="btn" style="background: var(--gray-500);">
                    <i class="fas fa-undo"></i> Limpar
                </a>
                <button type="button" onclick="exportarExcel()" class="btn" style="background: var(--success);">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </div>
    </form>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-trophy"></i> Desempenho dos Motoristas</span>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="desempenhoTable">
            <thead>
                 <tr>
                    <th>Motorista</th>
                    <th>Total Viagens</th>
                    <th>Total KM</th>
                    <th>Média KM/Viagem</th>
                    <th>Taxa Pontualidade</th>
                    <th>Ocorrências</th>
                    <th>Avaliação</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($desempenho as $item)
                 <tr>
                    <td><strong>{{ $item['motorista'] }}</strong></td>
                    <td>{{ $item['total_viagens'] }}</td>
                    <td>{{ number_format($item['total_km'], 0, ',', '.') }} km</td>
                    <td>{{ number_format($item['media_km_viagem'], 2, ',', '.') }} km</td>
                    <td>
                        <div style="background: #e2e8f0; border-radius: 10px; height: 8px; width: 100%; max-width: 100px;">
                            <div style="background: {{ $item['taxa_pontualidade'] >= 80 ? '#10b981' : ($item['taxa_pontualidade'] >= 60 ? '#f59e0b' : '#ef4444') }}; width: {{ $item['taxa_pontualidade'] }}%; height: 8px; border-radius: 10px;"></div>
                        </div>
                        {{ number_format($item['taxa_pontualidade'], 1) }}%
                    </td>
                    <td>
                        @if($item['ocorrencias'] > 0)
                            <span class="status-badge status-inativo">{{ $item['ocorrencias'] }}</span>
                        @else
                            <span class="status-badge status-ativo">0</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $avaliacaoClass = match($item['avaliacao']) {
                                'Excelente' => 'status-ativo',
                                'Bom' => 'status-pendente',
                                'Regular' => 'status-em-andamento',
                                default => 'status-inativo'
                            };
                        @endphp
                        <span class="status-badge {{ $avaliacaoClass }}">{{ $item['avaliacao'] }}</span>
                    </td>
                 </tr>
                @empty
                 <tr>
                    <td colspan="7" style="text-align: center;">Nenhum dado encontrado para o período selecionado.</td>
                 </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(!empty($desempenho))
    <div style="margin-top: 20px; padding: 20px; background: var(--gray-100); border-radius: 16px;">
        <h4>📊 Resumo do Período</h4>
        @php
            $totalViagens = collect($desempenho)->sum('total_viagens');
            $totalKm = collect($desempenho)->sum('total_km');
            $mediaPontualidade = collect($desempenho)->avg('taxa_pontualidade');
            $totalOcorrencias = collect($desempenho)->sum('ocorrencias');
            $melhorMotorista = collect($desempenho)->sortByDesc('taxa_pontualidade')->first();
        @endphp
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-top: 16px;">
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ $totalViagens }}</div>
                <div class="stat-title">Total de Viagens</div>
            </div>
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ number_format($totalKm, 0, ',', '.') }} km</div>
                <div class="stat-title">Total KM Rodados</div>
            </div>
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ number_format($mediaPontualidade, 1) }}%</div>
                <div class="stat-title">Média Pontualidade</div>
            </div>
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ $totalOcorrencias }}</div>
                <div class="stat-title">Total Ocorrências</div>
            </div>
        </div>
        @if($melhorMotorista)
        <div style="margin-top: 16px; padding: 12px; background: #d1fae5; border-radius: 12px;">
            <i class="fas fa-crown" style="color: #f59e0b;"></i>
            <strong>Melhor Motorista:</strong> {{ $melhorMotorista['motorista'] }} 
            (Pontualidade: {{ number_format($melhorMotorista['taxa_pontualidade'], 1) }}%)
        </div>
        @endif
    </div>
    @endif
</div>

<script>
    function exportarExcel() {
        let table = document.getElementById('desempenhoTable');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let downloadLink = document.createElement('a');
        downloadLink.href = url;
        downloadLink.download = 'relatorio_desempenho.xls';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
@endsection