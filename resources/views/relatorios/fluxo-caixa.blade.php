@extends('layouts.app')

@section('title', 'Fluxo de Caixa - SGTP')
@section('page-title', 'Relatório de Fluxo de Caixa')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-chart-line"></i> Filtros</span>
    </div>
    
    <form method="GET" action="{{ route('relatorios.fluxo-caixa') }}" id="filterForm">
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
                <label class="form-label">Saldo Inicial (Kz)</label>
                <input type="number" step="0.01" name="saldo_inicial" class="form-control" value="{{ request('saldo_inicial', 0) }}">
            </div>
            
            <div style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn-accent">
                    <i class="fas fa-search"></i> Calcular
                </button>
                <a href="{{ route('relatorios.fluxo-caixa') }}" class="btn" style="background: var(--gray-500);">
                    <i class="fas fa-undo"></i> Limpar
                </a>
                <button type="button" onclick="exportarExcel()" class="btn" style="background: var(--success);">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </div>
    </form>
</div>

@if(isset($dados))
<div class="row-cards" style="margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-calculator"></i> Resumo Financeiro</span>
        </div>
        
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="stat-card" style="text-align: center; background: #e0f2fe;">
                <div class="stat-value">Kz {{ number_format($dados['saldo_inicial'], 2, ',', '.') }}</div>
                <div class="stat-title">Saldo Inicial</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #d1fae5;">
                <div class="stat-value">Kz {{ number_format($dados['total_receitas'], 2, ',', '.') }}</div>
                <div class="stat-title">Total Receitas</div>
                <span>Entradas do período</span>
            </div>
            <div class="stat-card" style="text-align: center; background: #fee2e2;">
                <div class="stat-value">Kz {{ number_format($dados['total_despesas'], 2, ',', '.') }}</div>
                <div class="stat-title">Total Despesas</div>
                <span>Saídas do período</span>
            </div>
            <div class="stat-card" style="text-align: center; background: {{ $dados['resultado'] >= 0 ? '#d1fae5' : '#fee2e2' }};">
                <div class="stat-value">Kz {{ number_format($dados['resultado'], 2, ',', '.') }}</div>
                <div class="stat-title">Resultado</div>
                <span>{{ $dados['resultado'] >= 0 ? 'Lucro' : 'Prejuízo' }}</span>
            </div>
            <div class="stat-card" style="text-align: center; background: #fef3c7;">
                <div class="stat-value">Kz {{ number_format($dados['saldo_final'], 2, ',', '.') }}</div>
                <div class="stat-title">Saldo Final</div>
                <span>Disponível</span>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <div style="background: var(--gray-100); padding: 16px; border-radius: 12px;">
                <p><strong>Período Analisado:</strong> {{ \Carbon\Carbon::parse($dados['periodo']['inicio'])->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dados['periodo']['fim'])->format('d/m/Y') }}</p>
                <p><strong>Dias no período:</strong> {{ \Carbon\Carbon::parse($dados['periodo']['inicio'])->diffInDays(\Carbon\Carbon::parse($dados['periodo']['fim'])) + 1 }} dias</p>
                <p><strong>Receita Média Diária:</strong> Kz {{ number_format($dados['total_receitas'] / (\Carbon\Carbon::parse($dados['periodo']['inicio'])->diffInDays(\Carbon\Carbon::parse($dados['periodo']['fim'])) + 1), 2, ',', '.') }}</p>
                <p><strong>Despesa Média Diária:</strong> Kz {{ number_format($dados['total_despesas'] / (\Carbon\Carbon::parse($dados['periodo']['inicio'])->diffInDays(\Carbon\Carbon::parse($dados['periodo']['fim'])) + 1), 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-line"></i> Evolução Diária</span>
        </div>
        <canvas id="fluxoChart" height="250"></canvas>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-chart-pie"></i> Composição de Receitas e Despesas</span>
    </div>
    <div class="row-cards">
        <div>
            <canvas id="receitasPieChart" height="200"></canvas>
        </div>
        <div>
            <canvas id="despesasPieChart" height="200"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Gráfico de evolução diária
    const fluxoCtx = document.getElementById('fluxoChart')?.getContext('2d');
    if(fluxoCtx && @json(isset($evolucao))) {
        new Chart(fluxoCtx, {
            type: 'line',
            data: {
                labels: @json($evolucao['datas'] ?? []),
                datasets: [
                    {
                        label: 'Receitas',
                        data: @json($evolucao['receitas'] ?? []),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Despesas',
                        data: @json($evolucao['despesas'] ?? []),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }
    
    // Gráfico de receitas por origem
    const receitasPieCtx = document.getElementById('receitasPieChart')?.getContext('2d');
    if(receitasPieCtx && @json(isset($receitasPorOrigem))) {
        new Chart(receitasPieCtx, {
            type: 'pie',
            data: {
                labels: @json($receitasPorOrigem['labels'] ?? []),
                datasets: [{
                    data: @json($receitasPorOrigem['values'] ?? []),
                    backgroundColor: ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Receitas por Origem'
                    }
                }
            }
        });
    }
    
    // Gráfico de despesas por tipo
    const despesasPieCtx = document.getElementById('despesasPieChart')?.getContext('2d');
    if(despesasPieCtx && @json(isset($despesasPorTipo))) {
        new Chart(despesasPieCtx, {
            type: 'pie',
            data: {
                labels: @json($despesasPorTipo['labels'] ?? []),
                datasets: [{
                    data: @json($despesasPorTipo['values'] ?? []),
                    backgroundColor: ['#ef4444', '#f87171', '#fca5a5', '#fecaca']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Despesas por Tipo'
                    }
                }
            }
        });
    }
    
    function exportarExcel() {
        // Implementar exportação para Excel
        alert('Funcionalidade de exportação em desenvolvimento');
    }
</script>
@else
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-info-circle"></i> Selecione um período para visualizar o fluxo de caixa</span>
    </div>
</div>
@endif
@endsection