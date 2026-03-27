@extends('layouts.app')

@section('title', 'Usuários do Sistema - SGTP')
@section('page-title', 'Gestão de Usuários')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-users-cog"></i> Usuários do Sistema</span>
        @can('gerir_usuarios')
        <a href="{{ route('admin.usuarios.create') }}" class="btn-accent">
            <i class="fas fa-plus"></i> Novo Usuário
        </a>
        @endcan
    </div>
    
    <!-- Filtros -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" id="searchInput" placeholder="Buscar por nome ou e-mail..." class="form-control" style="width: 250px;">
        <select id="tipoFilter" class="form-control" style="width: 180px;">
            <option value="">Todos os tipos</option>
            <option value="ADMIN">Administrador</option>
            <option value="GESTOR_OPERACOES">Gestor Operações</option>
            <option value="GESTOR_FROTA">Gestor Frota</option>
            <option value="FISCAL">Fiscal</option>
            <option value="OPERADOR_BILHETICA">Operador Bilhética</option>
            <option value="FINANCEIRO">Financeiro</option>
        </select>
        <select id="statusFilter" class="form-control" style="width: 120px;">
            <option value="">Status</option>
            <option value="ATIVO">Ativo</option>
            <option value="INATIVO">Inativo</option>
            <option value="BLOQUEADO">Bloqueado</option>
        </select>
        <button id="filterBtn" class="btn-accent"><i class="fas fa-search"></i> Filtrar</button>
        <button id="resetBtn" class="btn"><i class="fas fa-undo"></i> Limpar</button>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="usuariosTable">
            <thead>
                 <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>BI</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>2FA</th>
                    <th>Último Acesso</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td><strong>{{ $usuario->name }}</strong></td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->bi ?? '--' }}</td>
                    <td>
                        @php
                            $tipoIcon = match($usuario->tipo_usuario) {
                                'ADMIN' => 'fa-crown',
                                'GESTOR_OPERACOES' => 'fa-chart-line',
                                'GESTOR_FROTA' => 'fa-truck',
                                'FISCAL' => 'fa-shield-alt',
                                'OPERADOR_BILHETICA' => 'fa-ticket-alt',
                                'FINANCEIRO' => 'fa-coins',
                                default => 'fa-user'
                            };
                            $tipoLabel = match($usuario->tipo_usuario) {
                                'ADMIN' => 'Administrador',
                                'GESTOR_OPERACOES' => 'Gestor Operações',
                                'GESTOR_FROTA' => 'Gestor Frota',
                                'FISCAL' => 'Fiscal',
                                'OPERADOR_BILHETICA' => 'Operador Bilhética',
                                'FINANCEIRO' => 'Financeiro',
                                default => $usuario->tipo_usuario
                            };
                        @endphp
                        <i class="fas {{ $tipoIcon }}"></i> {{ $tipoLabel }}
                    </td>
                    <td>
                        @php
                            $statusClass = match($usuario->status) {
                                'ATIVO' => 'status-ativo',
                                'INATIVO' => 'status-inativo',
                                'BLOQUEADO' => 'status-danger',
                                default => 'status-pendente'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $usuario->status }}</span>
                    </td>
                    <td>
                        @if($usuario->two_factor_enabled)
                            <span class="status-badge status-ativo"><i class="fas fa-check-circle"></i> Ativo</span>
                        @else
                            <span class="status-badge status-inativo"><i class="fas fa-times-circle"></i> Inativo</span>
                        @endif
                    </td>
                    <td>{{ $usuario->ultimo_acesso ? \Carbon\Carbon::parse($usuario->ultimo_acesso)->format('d/m/Y H:i') : '--' }}</td>
                    <td>
                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-sm" style="background: var(--warning);" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($usuario->status == 'ATIVO' && $usuario->id != Auth::id())
                        <form action="{{ route('admin.usuarios.bloquear', $usuario) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-sm" style="background: var(--danger);" title="Bloquear" onclick="return confirm('Bloquear este usuário?')">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @elseif($usuario->status != 'ATIVO' && $usuario->id != Auth::id())
                        <form action="{{ route('admin.usuarios.ativar', $usuario) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-sm" style="background: var(--success);" title="Ativar" onclick="return confirm('Ativar este usuário?')">
                                <i class="fas fa-check-circle"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Nenhum usuário cadastrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        {{ $usuarios->links() }}
    </div>
</div>

<!-- Estatísticas -->
<div class="row-cards" style="margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-chart-pie"></i> Estatísticas de Usuários</span>
        </div>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));">
            <div class="stat-card" style="text-align: center;">
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                <div class="stat-title">Total</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #d1fae5;">
                <div class="stat-value">{{ $stats['ativos'] ?? 0 }}</div>
                <div class="stat-title">Ativos</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #fee2e2;">
                <div class="stat-value">{{ $stats['bloqueados'] ?? 0 }}</div>
                <div class="stat-title">Bloqueados</div>
            </div>
            <div class="stat-card" style="text-align: center; background: #e0f2fe;">
                <div class="stat-value">{{ $stats['com_2fa'] ?? 0 }}</div>
                <div class="stat-title">2FA Ativo</div>
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
        
        document.querySelectorAll('#usuariosTable tbody tr').forEach(row => {
            let show = true;
            
            if(search && !row.textContent.toLowerCase().includes(search)) show = false;
            if(tipo && !row.cells[3]?.textContent.includes(tipo)) show = false;
            if(status && !row.cells[4]?.textContent.includes(status)) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    });
    
    document.getElementById('resetBtn')?.addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('tipoFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.querySelectorAll('#usuariosTable tbody tr').forEach(row => {
            row.style.display = '';
        });
    });
</script>
@endpush