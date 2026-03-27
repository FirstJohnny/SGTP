@extends('layouts.app')

@section('title', 'Receitas - SGTP')
@section('page-title', 'Gestão de Receitas')

@section('content')
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-coins"></i> Receitas</span>
            @can('gerir_financeiro')
                <button onclick="consolidarReceitas()" class="btn-accent">
                    <i class="fas fa-chart-line"></i> Consolidar Receitas
                </button>
            @endcan
        </div>

        <!-- Filtros -->
        <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="date" id="dataInicio" class="form-control" style="width: 150px;"
                value="{{ now()->startOfMonth()->format('Y-m-d') }}">
            <input type="date" id="dataFim" class="form-control" style="width: 150px;"
                value="{{ now()->format('Y-m-d') }}">
            <select id="origemFilter" class="form-control" style="width: 150px;">
                <option value="">Todas origens</option>
                <option value="BILHETE">Bilhete</option>
                <option value="SUBSIDIO">Subsídio</option>
                <option value="CONTRATO">Contrato</option>
                <option value="OUTROS">Outros</option>
            </select>
            <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
            <button id="resetBtn" class="btn"><i class="fas fa-undo"></i> Limpar</button>
        </div>

        <div class="table-responsive">
            <table class="data-table" id="receitasTable">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Origem</th>
                        <th>Descrição</th>
                        <th>Valor (Kz)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receitas as $receita)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($receita->data)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $origemIcon = match ($receita->origem) {
                                        'BILHETE' => 'fa-ticket-alt',
                                        'SUBSIDIO' => 'fa-hand-holding-usd',
                                        'CONTRATO' => 'fa-file-signature',
                                        default => 'fa-coins',
                                    };
                                @endphp
                                <i class="fas {{ $origemIcon }}"></i> {{ $receita->origem }}
                            </td>
                            <td>{{ $receita->descricao ?? '--' }}</td>
                            <td><strong>Kz {{ number_format($receita->valor_total, 2, ',', '.') }}</strong></td>
                            <td>
                                @if ($receita->consolidado)
                                    <span class="status-badge status-ativo">Consolidado</span>
                                @else
                                    <span class="status-badge status-em-andamento">Pendente</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">Nenhuma receita registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $receitas->links() }}
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row-cards" style="margin-top: 24px;">
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-chart-pie"></i> Resumo do Período</span>
            </div>
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); gap: 16px;">
                <div class="stat-card" style="text-align: center; background: #d1fae5;">
                    <div class="stat-value">Kz {{ number_format($totalReceitas ?? 0, 2, ',', '.') }}</div>
                    <div class="stat-title">Total Receitas</div>
                </div>
                <div class="stat-card" style="text-align: center; background: #e0f2fe;">
                    <div class="stat-value">{{ $bilhetesVendidos ?? 0 }}</div>
                    <div class="stat-title">Bilhetes Vendidos</div>
                </div>
                <div class="stat-card" style="text-align: center; background: #fed7aa;">
                    <div class="stat-value">Kz {{ number_format($mediaDiaria ?? 0, 2, ',', '.') }}</div>
                    <div class="stat-title">Média Diária</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-chart-line"></i> Evolução das Receitas</span>
            </div>
            <canvas id="receitasChart" height="200"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Gráfico de evolução
        const ctx = document.getElementById('receitasChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels ?? []),
                    datasets: [{
                        label: 'Receitas (Kz)',
                        data: @json($chartValues ?? []),
                        borderColor: '#1a4d8c',
                        backgroundColor: 'rgba(26, 77, 140, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
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

        function consolidarReceitas() {
            if (confirm('Deseja consolidar as receitas do período selecionado? Esta ação não pode ser desfeita.')) {
                fetch('{{ route('receitas.consolidar') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            data_inicio: document.getElementById('dataInicio').value,
                            data_fim: document.getElementById('dataFim').value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Receitas consolidadas com sucesso!');
                            location.reload();
                        } else {
                            alert('Erro ao consolidar: ' + data.message);
                        }
                    });
            }
        }

        document.getElementById('filterBtn')?.addEventListener('click', function() {
            const dataInicio = document.getElementById('dataInicio').value;
            const dataFim = document.getElementById('dataFim').value;
            const origem = document.getElementById('origemFilter').value;

            window.location.href = '{{ route('receitas.index') }}?data_inicio=' + dataInicio + '&data_fim=' +
                dataFim + '&origem=' + origem;
        });

        document.getElementById('resetBtn')?.addEventListener('click', function() {
            window.location.href = '{{ route('receitas.index') }}';
        });
    </script>
@endsection
