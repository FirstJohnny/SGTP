@extends('layouts.app')

@section('title', 'Configurações - SGTP')
@section('page-title', 'Configurações do Sistema')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-cog"></i> Configurações Gerais</span>
    </div>
    
    <form action="{{ route('admin.configuracoes.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Nome do Sistema</label>
                    <input type="text" name="sistema_nome" class="form-control" value="{{ $configuracoes['sistema_nome'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">E-mail do Sistema</label>
                    <input type="email" name="sistema_email" class="form-control" value="{{ $configuracoes['sistema_email'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Fuso Horário</label>
                    <select name="fuso_horario" class="form-control">
                        <option value="Africa/Luanda" {{ $configuracoes['fuso_horario'] == 'Africa/Luanda' ? 'selected' : '' }}>Africa/Luanda</option>
                        <option value="UTC" {{ $configuracoes['fuso_horario'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Formato de Data</label>
                    <select name="formato_data" class="form-control">
                        <option value="d/m/Y" {{ $configuracoes['formato_data'] == 'd/m/Y' ? 'selected' : '' }}>dd/mm/yyyy</option>
                        <option value="Y-m-d" {{ $configuracoes['formato_data'] == 'Y-m-d' ? 'selected' : '' }}>yyyy-mm-dd</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label class="form-label">Limite de Horas de Condução</label>
                    <input type="number" name="limite_horas_conducao" class="form-control" value="{{ $configuracoes['limite_horas_conducao'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">KM para Manutenção Preventiva</label>
                    <input type="number" name="km_manutencao_preventiva" class="form-control" value="{{ $configuracoes['km_manutencao_preventiva'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Alerta de Vencimento (dias)</label>
                    <input type="number" name="alerta_vencimento_dias" class="form-control" value="{{ $configuracoes['alerta_vencimento_dias'] }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Atualização GPS (segundos)</label>
                    <input type="number" name="gps_atualizacao_segundos" class="form-control" value="{{ $configuracoes['gps_atualizacao_segundos'] }}">
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('dashboard') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Salvar Configurações
            </button>
        </div>
    </form>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-database"></i> Manutenção</span>
    </div>
    <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div>
            <button onclick="limparCache()" class="btn" style="background: var(--warning);">
                <i class="fas fa-broom"></i> Limpar Cache
            </button>
        </div>
        <div>
            <button onclick="gerarBackup()" class="btn" style="background: var(--info);">
                <i class="fas fa-database"></i> Gerar Backup
            </button>
        </div>
    </div>
</div>

<script>
    function limparCache() {
        if(confirm('Tem certeza que deseja limpar o cache do sistema?')) {
            fetch('{{ route("admin.configuracoes.clear-cache") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Cache limpo com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao limpar cache. Tente novamente.');
            });
        }
    }
    
    function gerarBackup() {
    let tipo = prompt('Tipo de backup (database, storage, full):', 'database');
    if(tipo && tipo.trim()) {
        // Validar o tipo
        const tiposValidos = ['database', 'storage', 'full'];
        if(!tiposValidos.includes(tipo.toLowerCase())) {
            alert('Tipo inválido. Use: database, storage ou full');
            return;
        }
        
        // Redirecionar para a rota com o parâmetro
        window.location.href = '{{ route("admin.configuracoes.backup") }}?tipo=' + encodeURIComponent(tipo.toLowerCase());
    }
}
</script>
@endsection