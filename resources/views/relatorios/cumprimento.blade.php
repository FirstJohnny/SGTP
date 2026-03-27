@extends('layouts.app')

@section('title', 'Relatório de Cumprimento de Horários - SGTP')
@section('page-title', 'Relatório de Cumprimento de Horários')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-chart-line"></i> Filtros</span>
    </div>
    
    <form method="GET" action="{{ route('relatorios.cumprimento-horarios') }}" id="filterForm">
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="form-group">
                <label class="form-label">Data Início</label>
                <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio', now()->startOfMonth()->format('Y-m-d')) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Data Fim</label>
                <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim', now()->format('Y-m-d')) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Rota</label>
                <select name="rota_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach($rotas ?? [] as $rota)
                    <option value="{{ $rota->id }}" {{ request('rota_id') == $rota->id ? 'selected' : '' }}>{{ $rota->nome }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Motorista</label>
                <select name="motorista_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach($motoristas ?? [] as $motorista)
                    <option value="{{ $motorista->id }}" {{ request('motorista_id') == $motorista->id ? 'selected' : '' }}>{{ $motorista->nome_completo }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn-accent">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('relatorios.cumprimento-horarios') }}" class="btn" style="background: var(--gray-500);">
                    <i class="fas fa-undo"></i> Limpar
                </a>
                <button type="button" onclick="exportarExcel()" class="btn" style="background: var(--success);">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button type="button" onclick="window.print()" class="btn" style="background: var(--info);">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </form>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-table"></i> Resultados</span>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="relatorioTable">
            <thead>
                 <tr>
                    <th>Data</th>
                    <th>Rota</th>
                    <th>Motorista</th>
                    <th>Veículo</th>
                    <th>Hora Prevista</th>
                    <th>Hora Real</th>
                    <th>Atraso</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dados as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item['data'])->format('d/m/Y') }}</td>
                    <td>{{ $item['rota'] }}</td>
                    <td>{{ $item['motorista'] }}</td>
                    <td>{{ $item['veiculo'] }}</td>
                    <td>{{ $item['hora_prevista'] }}</td>
                    <td>{{ $item['hora_real'] }}</td>
                    <td>{{ $item['atraso'] }}</td>
                    <td>
                        <span class="status-badge 
                            @if($item['status'] == 'No horário') status-ativo
                            @elseif($item['status'] == 'Atraso < 10min') status-em-andamento
                            @else status-inativo @endif">
                            {{ $item['status'] }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Nenhum dado encontrado para o período selecionado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(!empty($dados))
    <div style="margin-top: 20px; padding: 20px; background: var(--gray-100); border-radius: 16px;">
        <h4>📊 Resumo do Período</h4>
        @php
            $total = count($dados);
            $noHorario = collect($dados)->where('status', 'No horário')->count();
            $atrasoMenor = collect($dados)->where('status', 'Atraso < 10min')->count();
            $atrasoMaior = collect($dados)->where('status', 'Atraso > 15min')->count();
            $naoIniciadas = collect($dados)->where('hora_real', 'Não iniciada')->count();
        @endphp
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-top: 16px;">
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ $total }}</div>
                <div class="stat-title">Total de Viagens</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #d1fae5;">
                <div class="stat-value">{{ $noHorario }}</div>
                <div class="stat-title">No Horário</div>
                <span>{{ $total > 0 ? round(($noHorario / $total) * 100, 1) : 0 }}%</span>
            </div>
            <div class="stat-card" style="text-align: center; background: #fed7aa;">
                <div class="stat-value">{{ $atrasoMenor }}</div>
                <div class="stat-title">Atraso < 10min</div>
                <span>{{ $total > 0 ? round(($atrasoMenor / $total) * 100, 1) : 0 }}%</span>
            </div>
            <div class="stat-card" style="text-align: center; background: #fee2e2;">
                <div class="stat-value">{{ $atrasoMaior }}</div>
                <div class="stat-title">Atraso > 15min</div>
                <span>{{ $total > 0 ? round(($atrasoMaior / $total) * 100, 1) : 0 }}%</span>
            </div>
            <div class="stat-card" style="text-align: center; background: #e0f2fe;">
                <div class="stat-value">{{ $naoIniciadas }}</div>
                <div class="stat-title">Não Iniciadas</div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function exportarExcel() {
        let table = document.getElementById('relatorioTable');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let downloadLink = document.createElement('a');
        downloadLink.href = url;
        downloadLink.download = 'relatorio_cumprimento.xls';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
@endpush