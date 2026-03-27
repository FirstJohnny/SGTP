@extends('layouts.app')

@section('title', 'Logs de Auditoria - SGTP')
@section('page-title', 'Logs de Auditoria')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-history"></i> Registros de Auditoria</span>
        <div>
            <button onclick="exportarLogs()" class="btn-sm" style="background: var(--success);">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <button onclick="limparLogs()" class="btn-sm" style="background: var(--danger);">
                <i class="fas fa-trash"></i> Limpar Antigos
            </button>
        </div>
    </div>
    
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <select id="usuarioFilter" class="form-control" style="width: 200px;">
            <option value="">Todos os usuários</option>
            @foreach($usuarios as $usuario)
            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
            @endforeach
        </select>
        <select id="acaoFilter" class="form-control" style="width: 150px;">
            <option value="">Todas ações</option>
            @foreach($acoes as $acao)
            <option value="{{ $acao }}">{{ $acao }}</option>
            @endforeach
        </select>
        <input type="text" id="entidadeFilter" placeholder="Entidade..." class="form-control" style="width: 150px;">
        <input type="date" id="dataInicio" class="form-control" style="width: 130px;">
        <input type="date" id="dataFim" class="form-control" style="width: 130px;">
        <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
        <button id="resetBtn" class="btn"><i class="fas fa-undo"></i> Limpar</button>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="auditoriaTable">
            <thead>
                 <tr>
                    <th>Data/Hora</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Entidade</th>
                    <th>ID</th>
                    <th>IP</th>
                    <th>Detalhes</th>
                  </thead>
            <tbody>
                @forelse($logs as $log)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($log->timestamp)->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->usuario->name ?? 'N/A' }}</td>
                    <td><span class="status-badge status-info">{{ $log->acao }}</span></td>
                    <td>{{ $log->entidade }}</td>
                    <td>{{ $log->entidade_id ?? '--' }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>
                        <a href="{{ route('admin.auditoria.show', $log) }}" class="btn-sm" style="background: var(--info);">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7">Nenhum log encontrado.</td></tr>
                @endforelse
            </tbody>
          </table>
    </div>
    
    <div class="pagination">
        {{ $logs->links() }}
    </div>
</div>

<script>
    function exportarLogs() {
        window.location.href = '{{ route("admin.auditoria.export") }}?' + new URLSearchParams({
            usuario_id: document.getElementById('usuarioFilter').value,
            acao: document.getElementById('acaoFilter').value,
            data_inicio: document.getElementById('dataInicio').value,
            data_fim: document.getElementById('dataFim').value
        });
    }
    
    function limparLogs() {
        let anos = prompt('Quantos anos de logs deseja manter? (mínimo 1, máximo 10)');
        if(anos && anos >= 1 && anos <= 10) {
            fetch('{{ route("admin.auditoria.limpar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ anos_retencao: anos })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Logs removidos com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        }
    }
    
    document.getElementById('filterBtn')?.addEventListener('click', function() {
        const params = new URLSearchParams();
        if(document.getElementById('usuarioFilter').value) params.set('usuario_id', document.getElementById('usuarioFilter').value);
        if(document.getElementById('acaoFilter').value) params.set('acao', document.getElementById('acaoFilter').value);
        if(document.getElementById('entidadeFilter').value) params.set('entidade', document.getElementById('entidadeFilter').value);
        if(document.getElementById('dataInicio').value) params.set('data_inicio', document.getElementById('dataInicio').value);
        if(document.getElementById('dataFim').value) params.set('data_fim', document.getElementById('dataFim').value);
        window.location.href = '{{ route("admin.auditoria.index") }}?' + params.toString();
    });
    
    document.getElementById('resetBtn')?.addEventListener('click', function() {
        window.location.href = '{{ route("admin.auditoria.index") }}';
    });
</script>
@endsection