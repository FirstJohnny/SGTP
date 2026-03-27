@extends('layouts.app')

@section('title', 'Dashboard - SGTP')
@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid" id="statsGrid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-bus"></i></div>
        <div class="stat-title">Frota Ativa</div>
        <div class="stat-value" id="frotaAtiva">--</div>
        <span>Carregando...</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-charging-station"></i></div>
        <div class="stat-title">Rotas Operacionais</div>
        <div class="stat-value" id="rotasAtivas">--</div>
        <span>Carregando...</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-title">Passageiros (hoje)</div>
        <div class="stat-value" id="passageirosHoje">--</div>
        <span>Carregando...</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-coins"></i></div>
        <div class="stat-title">Receita do Dia</div>
        <div class="stat-value" id="receitaDia">--</div>
        <span>Carregando...</span>
    </div>
</div>

<div class="row-cards">
    <div class="card">
        <div class="card-header">
            <span>📊 Ocupação Média por Rota</span>
            <i class="fas fa-chart-line"></i>
        </div>
        <canvas id="ocupacaoChart" height="200"></canvas>
    </div>
    <div class="card">
        <div class="card-header">
            <span>🕒 Cumprimento de Horários</span>
            <i class="fas fa-clock"></i>
        </div>
        <canvas id="horariosChart" height="200"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>🚌 Últimas Ocorrências</span>
        <a href="{{ route('ocorrencias.create') }}" class="btn-accent" style="text-decoration: none; padding: 6px 12px; font-size: 0.8rem;">
            <i class="fas fa-plus"></i> Nova Ocorrência
        </a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Veículo</th>
                    <th>Tipo</th>
                    <th>Gravidade</th>
                    <th>Data</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="ultimasOcorrencias">
                <tr><td colspan="5" style="text-align: center;">Carregando...</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Carregar estatísticas
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('frotaAtiva').textContent = data.data.frota_ativa;
                    document.getElementById('rotasAtivas').textContent = data.data.rotas_ativas;
                    document.getElementById('passageirosHoje').textContent = data.data.passageiros_hoje.toLocaleString();
                    document.getElementById('receitaDia').textContent = 'Kz ' + parseFloat(data.data.receita_dia).toLocaleString();
                }
            })
            .catch(error => console.error('Erro:', error));
        
        // Carregar gráfico de ocupação
        fetch('/api/dashboard/ocupacao')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('ocupacaoChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Ocupação média (%)',
                            data: data.values,
                            backgroundColor: '#1a4d8c',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            })
            .catch(error => console.error('Erro:', error));
        
        // Carregar gráfico de cumprimento de horários
        fetch('/api/dashboard/cumprimento')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('horariosChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['No horário', 'Atraso < 10min', 'Atraso > 15min'],
                        datasets: [{
                            data: [data.no_horario, data.atraso_menor_10, data.atraso_maior_15],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Erro:', error));
        
        // Carregar últimas ocorrências
        fetch('/api/ocorrencias/ultimas')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('ultimasOcorrencias');
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Nenhuma ocorrência registrada</td></tr>';
                } else {
                    tbody.innerHTML = data.map(oc => `
                        <tr>
                            <td>${oc.veiculo_placa}</td>
                            <td>${oc.tipo}</td>
                            <td>${oc.gravidade}</td>
                            <td>${oc.data_ocorrencia}</td>
                            <td><span class="status-badge status-${oc.status_class}">${oc.status}</span></td>
                        </tr>
                    `).join('');
                }
            })
            .catch(error => console.error('Erro:', error));
    });
</script>
@endpush