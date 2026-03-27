@extends('layouts.app')

@section('title', 'Ocorrências - SGTP')
@section('page-title', 'Gestão de Ocorrências')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-exclamation-triangle"></i> Ocorrências Registradas</span>
        @can('registrar_ocorrencias')
        <a href="{{ route('ocorrencias.create') }}" class="btn-accent">
            <i class="fas fa-plus"></i> Nova Ocorrência
        </a>
        @endcan
    </div>
    
    <!-- Filtros -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" id="searchInput" placeholder="Buscar por veículo ou descrição..." class="form-control" style="width: 250px;">
        <select id="tipoFilter" class="form-control" style="width: 150px;">
            <option value="">Todos os tipos</option>
            <option value="ACIDENTE">Acidente</option>
            <option value="ATRASO">Atraso</option>
            <option value="FALHA_MECANICA">Falha Mecânica</option>
            <option value="ASSALTO">Assalto</option>
        </select>
        <select id="gravidadeFilter" class="form-control" style="width: 120px;">
            <option value="">Gravidade</option>
            <option value="LEVE">Leve</option>
            <option value="MEDIA">Média</option>
            <option value="GRAVE">Grave</option>
            <option value="CRITICA">Crítica</option>
        </select>
        <select id="statusFilter" class="form-control" style="width: 130px;">
            <option value="">Status</option>
            <option value="ABERTA">Aberta</option>
            <option value="EM_ANALISE">Em Análise</option>
            <option value="RESOLVIDA">Resolvida</option>
        </select>
        <input type="date" id="dataInicio" class="form-control" style="width: 130px;">
        <input type="date" id="dataFim" class="form-control" style="width: 130px;">
        <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
        <button id="resetBtn" class="btn"><i class="fas fa-undo"></i> Limpar</button>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="ocorrenciasTable">
            <thead>
                 <tr>
                    <th>Data/Hora</th>
                    <th>Veículo</th>
                    <th>Tipo</th>
                    <th>Gravidade</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                 </thead>
            <tbody>
                @forelse($ocorrencias as $ocorrencia)
                  <tr style="{{ $ocorrencia->gravidade == 'CRITICA' ? 'background: #fee2e2;' : '' }}">
                    <td>{{ \Carbon\Carbon::parse($ocorrencia->data_ocorrencia)->format('d/m/Y H:i') }}</td>
                    <td>{{ $ocorrencia->veiculo->placa ?? 'N/A' }}</td>
                    <td>
                        @php
                            $tipoIcon = match($ocorrencia->tipo) {
                                'ACIDENTE' => 'fa-car-crash',
                                'ATRASO' => 'fa-clock',
                                'FALHA_MECANICA' => 'fa-tools',
                                'ASSALTO' => 'fa-shield-alt',
                                default => 'fa-exclamation-circle'
                            };
                        @endphp
                        <i class="fas {{ $tipoIcon }}"></i> {{ $ocorrencia->tipo }}
                    </td>
                    <td>
                        @php
                            $gravidadeClass = match($ocorrencia->gravidade) {
                                'LEVE' => 'status-pendente',
                                'MEDIA' => 'status-em-andamento',
                                'GRAVE' => 'status-inativo',
                                'CRITICA' => 'status-danger'
                            };
                        @endphp
                        <span class="status-badge {{ $gravidadeClass }}">{{ $ocorrencia->gravidade }}</span>
                    </td>
                    <td>{{ Str::limit($ocorrencia->descricao, 50) }}</td>
                    <td>
                        @php
                            $statusClass = match($ocorrencia->status) {
                                'ABERTA' => 'status-pendente',
                                'EM_ANALISE' => 'status-em-andamento',
                                'RESOLVIDA' => 'status-ativo',
                                default => 'status-inativo'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $ocorrencia->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('ocorrencias.show', $ocorrencia) }}" class="btn-sm" style="background: var(--info);">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('gerir_ocorrencias')
                        <button onclick="editarStatus({{ $ocorrencia->id }}, '{{ $ocorrencia->status }}')" class="btn-sm" style="background: var(--warning);">
                            <i class="fas fa-edit"></i>
                        </button>
                        @endcan
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" style="text-align: center;">Nenhuma ocorrência registrada.</td>
                  </tr>
                @endforelse
            </tbody>
          </table>
    </div>
    
    <div class="pagination">
        {{ $ocorrencias->links() }}
    </div>
</div>

<!-- Modal para editar status -->
<div id="statusModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 24px; padding: 24px; width: 400px; max-width: 90%;">
        <h3>Atualizar Status</h3>
        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" id="statusSelect" class="form-control" required>
                    <option value="ABERTA">Aberta</option>
                    <option value="EM_ANALISE">Em Análise</option>
                    <option value="RESOLVIDA">Resolvida</option>
                    <option value="CANCELADA">Cancelada</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                <button type="button" onclick="fecharModal()" class="btn" style="background: var(--gray-500);">Cancelar</button>
                <button type="submit" class="btn-accent">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editarStatus(id, statusAtual) {
        const modal = document.getElementById('statusModal');
        const form = document.getElementById('statusForm');
        const select = document.getElementById('statusSelect');
        
        form.action = '/ocorrencias/' + id;
        select.value = statusAtual;
        modal.style.display = 'flex';
    }
    
    function fecharModal() {
        document.getElementById('statusModal').style.display = 'none';
    }
    
    document.getElementById('filterBtn')?.addEventListener('click', function() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const tipo = document.getElementById('tipoFilter').value;
        const gravidade = document.getElementById('gravidadeFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        document.querySelectorAll('#ocorrenciasTable tbody tr').forEach(row => {
            let show = true;
            
            if(search && !row.textContent.toLowerCase().includes(search)) show = false;
            if(tipo && !row.cells[2]?.textContent.includes(tipo)) show = false;
            if(gravidade && !row.cells[3]?.textContent.includes(gravidade)) show = false;
            if(status && !row.cells[5]?.textContent.includes(status)) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    });
    
    document.getElementById('resetBtn')?.addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('tipoFilter').value = '';
        document.getElementById('gravidadeFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.querySelectorAll('#ocorrenciasTable tbody tr').forEach(row => {
            row.style.display = '';
        });
    });
</script>
@endpush