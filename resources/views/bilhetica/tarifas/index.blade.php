@extends('layouts.app')

@section('title', 'Tarifas - SGTP')
@section('page-title', 'Gestão de Tarifas')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-tags"></i> Tarifas por Rota</span>
        @can('gerir_tarifas')
        <button onclick="abrirModal()" class="btn-accent">
            <i class="fas fa-plus"></i> Nova Tarifa
        </button>
        @endcan
    </div>
    
    <!-- Filtros -->
    <form action="{{ route('tarifas.index') }}" method="GET" style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <select name="rota_id" id="rotaFilter" class="form-control" style="width: 250px;">
            <option value="">Todas as rotas</option>
            @foreach($rotas as $rota)
            <option value="{{ $rota->id }}" {{ request('rota_id') == $rota->id ? 'selected' : '' }}>{{ $rota->nome }} ({{ $rota->codigo }})</option>
            @endforeach
        </select>
        <select name="status" id="statusFilter" class="form-control" style="width: 120px;">
            <option value="">Status</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativas</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativas</option>
        </select>
        <button type="submit" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
        <a href="{{ route('tarifas.index') }}" class="btn"><i class="fas fa-undo"></i> Limpar</a>
    </form>
    
    <div class="table-responsive">
        <table class="data-table" id="tarifasTable">
            <thead>
                  <tr>
                    <th>Rota</th>
                    <th>Tipo Passageiro</th>
                    <th>Valor (Kz)</th>
                    <th>Vigência</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
            </thead>
            <tbody>
                @forelse($tarifas as $tarifa)
                  <tr>
                    <td><strong>{{ $tarifa->rota->nome ?? 'N/A' }}</strong><br><small>{{ $tarifa->rota->codigo ?? '' }}</small></td>
                    <td>
                        @php
                            $tipoIcon = match($tarifa->tipo_passageiro) {
                                'ADULTO' => 'fa-user',
                                'ESTUDANTE' => 'fa-graduation-cap',
                                'IDOSO' => 'fa-user-plus',
                                default => 'fa-user'
                            };
                        @endphp
                        <i class="fas {{ $tipoIcon }}"></i> {{ $tarifa->tipo_passageiro }}
                    </td>
                    <td><strong>Kz {{ number_format($tarifa->valor, 2, ',', '.') }}</strong></td>
                    <td>
                        {{ \Carbon\Carbon::parse($tarifa->data_inicio)->format('d/m/Y') }}
                        @if($tarifa->data_fim)
                            até {{ \Carbon\Carbon::parse($tarifa->data_fim)->format('d/m/Y') }}
                        @else
                            (sem data fim)
                        @endif
                    </td>
                    <td>
                        @if($tarifa->ativa && $tarifa->isVigente())
                            <span class="status-badge status-ativo">Vigente</span>
                        @elseif($tarifa->ativa && !$tarifa->isVigente())
                            <span class="status-badge status-em-andamento">Aguardando</span>
                        @else
                            <span class="status-badge status-inativo">Inativa</span>
                        @endif
                    </td>
                    <td>
                        @can('gerir_tarifas')
                        <button onclick="editarTarifa({{ $tarifa->id }}, {{ $tarifa->valor }}, {{ $tarifa->ativa ? 'true' : 'false' }}, '{{ $tarifa->data_fim }}')" class="btn-sm" style="background: var(--warning);">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('tarifas.destroy', $tarifa) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm" style="background: var(--danger);" onclick="return confirm('Tem certeza que deseja remover esta tarifa?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" style="text-align: center;">Nenhuma tarifa cadastrada.</td>
                  </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $tarifas->links() }}
    </div>
</div>

<!-- Modal para Nova Tarifa -->
<div id="tarifaModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 24px; padding: 24px; width: 500px; max-width: 90%;">
        <h3 id="modalTitle">Nova Tarifa</h3>
        <form id="tarifaForm" method="POST" action="{{ route('tarifas.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Rota <span style="color: red;">*</span></label>
                <select name="rota_id" class="form-control @error('rota_id') is-invalid @enderror" required>
                    <option value="">Selecione...</option>
                    @foreach($rotas as $rota)
                    <option value="{{ $rota->id }}" {{ old('rota_id') == $rota->id ? 'selected' : '' }}>{{ $rota->nome }} ({{ $rota->codigo }})</option>
                    @endforeach
                </select>
                @error('rota_id') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Tipo Passageiro <span style="color: red;">*</span></label>
                <select name="tipo_passageiro" class="form-control @error('tipo_passageiro') is-invalid @enderror" required>
                    <option value="ADULTO">Adulto</option>
                    <option value="ESTUDANTE">Estudante</option>
                    <option value="IDOSO">Idoso</option>
                    <option value="OUTRO">Outro</option>
                </select>
                @error('tipo_passageiro') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Valor (Kz) <span style="color: red;">*</span></label>
                <input type="number" step="0.01" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor') }}" required>
                @error('valor') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Data Início <span style="color: red;">*</span></label>
                <input type="date" name="data_inicio" class="form-control @error('data_inicio') is-invalid @enderror" value="{{ old('data_inicio', date('Y-m-d')) }}" required>
                @error('data_inicio') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Data Fim (opcional)</label>
                <input type="date" name="data_fim" class="form-control">
            </div>
            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" name="ativa" value="1" checked>
                    <span>Ativa</span>
                </label>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                <button type="button" onclick="fecharModal()" class="btn" style="background: var(--gray-500);">Cancelar</button>
                <button type="submit" class="btn-accent">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Editar Tarifa -->
<div id="editTarifaModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 24px; padding: 24px; width: 400px; max-width: 90%;">
        <h3>Editar Tarifa</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Valor (Kz)</label>
                <input type="number" step="0.01" name="valor" id="editValor" class="form-control @error('valor', 'update') is-invalid @enderror" required>
                @error('valor', 'update') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Data Fim</label>
                <input type="date" name="data_fim" id="editDataFim" class="form-control @error('data_fim', 'update') is-invalid @enderror">
                @error('data_fim', 'update') <small style="color: var(--danger);">{{ $message }}</small> @enderror
            </div>
            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" name="ativa" id="editAtiva" value="1">
                    <span>Ativa</span>
                </label>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                <button type="button" onclick="fecharEditModal()" class="btn" style="background: var(--gray-500);">Cancelar</button>
                <button type="submit" class="btn-accent">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function abrirModal() {
        document.getElementById('tarifaModal').style.display = 'flex';
    }
    
    function fecharModal() {
        document.getElementById('tarifaModal').style.display = 'none';
    }
    
    function editarTarifa(id, valor, ativa, dataFim) {
        const modal = document.getElementById('editTarifaModal');
        const form = document.getElementById('editForm');
        form.action = '/tarifas/' + id;
        document.getElementById('editValor').value = valor;
        document.getElementById('editAtiva').checked = ativa;
        document.getElementById('editDataFim').value = dataFim || '';
        modal.style.display = 'flex';
    }
    
    function fecharEditModal() {
        document.getElementById('editTarifaModal').style.display = 'none';
    }

    // Reabre o modal caso existam erros de validação após o submit
    @if($errors->any())
        @if($errors->hasBag('update'))
            document.getElementById('editTarifaModal').style.display = 'flex';
        @else
            document.getElementById('tarifaModal').style.display = 'flex';
        @endif
    @endif
</script>
@endpush