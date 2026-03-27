@extends('layouts.app')

@section('title', 'Relatório de Ocupação de Veículos - SGTP')
@section('page-title', 'Relatório de Ocupação de Veículos')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-chart-bar"></i> Filtros</span>
    </div>
    
    <form method="GET" action="{{ route('relatorios.ocupacao-veiculos') }}" id="filterForm">
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
                <label class="form-label">Rota</label>
                <select name="rota_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach($rotas as $rota)
                    <option value="{{ $rota->id }}" {{ request('rota_id') == $rota->id ? 'selected' : '' }}>
                        {{ $rota->nome }} ({{ $rota->codigo }})
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn-accent">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('relatorios.ocupacao-veiculos') }}" class="btn" style="background: var(--gray-500);">
                    <i class="fas fa-undo"></i> Limpar
                </a>
                <button type="button" onclick="exportarExcel()" class="btn" style="background: var(--success);">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </div>
    </form>
</div>

<div class="row-cards" style="margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-pie"></i> Ocupação por {{ request('rota_id') ? 'Veículo' : 'Rota' }}</span>
        </div>
        <canvas id="ocupacaoChart" height="300"></canvas>
    </div>
    
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-table"></i> Dados Detalhados</span>
        </div>
        <div class="table-responsive">
            <table class="data-table" id="ocupacaoTable">
                <thead>
                     <tr>
                        <th>{{ request('rota_id') ? 'Veículo' : 'Rota' }}</th>
                        <th>Total Passageiros</th>
                        <th>Total Viagens</th>
                        <th>Média Passageiros/Viagem</th>
                     </tr>
                </thead>
                <tbody>
                    @forelse($ocupacao as $item)
                     <tr>
                        <td><strong>{{ $item['item'] }}</strong></td>
                        <td>{{ number_format($item['total_passageiros'], 0, ',', '.') }}</td>
                        <td>{{ $item['total_viagens'] }}</td>
                        <td>
                            <div style="background: #e2e8f0; border-radius: 10px; height: 8px; width: 100%; max-width: 100px;">
                                <div style="background: #1a4d8c; width: {{ ($item['media_passageiros_viagem'] / 50) * 100 }}%; height: 8px; border-radius: 10px;"></div>
                            </div>
                            {{ number_format($item['media_passageiros_viagem'], 1) }} passageiros
                        </td>
                     </tr>
                    @empty
                     <tr>
                        <td colspan="4" style="text-align: center;">Nenhum dado encontrado para o período selecionado.</td>
                     </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Gráfico de ocupação
    const ctx = document.getElementById('ocupacaoChart')?.getContext('2d');
    if(ctx && {{ count($ocupacao) }} > 0) {
        const labels = @json(collect($ocupacao)->pluck('item'));
        const data = @json(collect($ocupacao)->pluck('media_passageiros_viagem'));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Média de Passageiros por Viagem',
                    data: data,
                    backgroundColor: '#1a4d8c',
                    borderRadius: 8,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Passageiros'
                        }
                    }
                }
            }
        });
    }
    
    function exportarExcel() {
        let table = document.getElementById('ocupacaoTable');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        let downloadLink = document.createElement('a');
        downloadLink.href = url;
        downloadLink.download = 'relatorio_ocupacao.xls';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>
@endsection