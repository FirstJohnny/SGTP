@extends('layouts.app')

@section('title', 'Novo Colaborador - SGTP')
@section('page-title', 'Cadastrar Novo Colaborador')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-user-plus"></i> Dados do Colaborador</span>
        <a href="{{ route('colaboradores.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('colaboradores.store') }}" method="POST" id="colaboradorForm">
        @csrf
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Tipo <span style="color: red;">*</span></label>
                    <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="MOTORISTA" {{ old('tipo') == 'MOTORISTA' ? 'selected' : '' }}>Motorista</option>
                        <option value="COBRADOR" {{ old('tipo') == 'COBRADOR' ? 'selected' : '' }}>Cobrador</option>
                        <option value="FISCAL" {{ old('tipo') == 'FISCAL' ? 'selected' : '' }}>Fiscal</option>
                        <option value="OUTRO" {{ old('tipo') == 'OUTRO' ? 'selected' : '' }}>Outro</option>
                    </select>
                    @error('tipo') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nome Completo <span style="color: red;">*</span></label>
                    <input type="text" name="nome_completo" class="form-control @error('nome_completo') is-invalid @enderror" value="{{ old('nome_completo') }}" required>
                    @error('nome_completo') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">BI <span style="color: red;">*</span></label>
                    <input type="text" name="bi" class="form-control @error('bi') is-invalid @enderror" value="{{ old('bi') }}" required>
                    @error('bi') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Data de Contratação <span style="color: red;">*</span></label>
                    <input type="date" name="data_contratacao" class="form-control @error('data_contratacao') is-invalid @enderror" value="{{ old('data_contratacao', date('Y-m-d')) }}" required>
                    @error('data_contratacao') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Telefone <span style="color: red;">*</span></label>
                    <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone') }}" required>
                    @error('telefone') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Salário Base <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="salario_base" class="form-control @error('salario_base') is-invalid @enderror" value="{{ old('salario_base') }}" required>
                    @error('salario_base') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nº Segurança Social <span style="color: red;">*</span></label>
                    <input type="text" name="numero_seguranca_social" class="form-control @error('numero_seguranca_social') is-invalid @enderror" value="{{ old('numero_seguranca_social') }}" required>
                    @error('numero_seguranca_social') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div id="cartaFields" style="{{ old('tipo') == 'MOTORISTA' ? '' : 'display: none;' }}">
                <div class="form-group">
                    <label class="form-label">Nº Carta de Condução</label>
                    <input type="text" name="numero_carta" class="form-control" value="{{ old('numero_carta') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Validade da Carta</label>
                    <input type="date" name="carta_validade" class="form-control" value="{{ old('carta_validade') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Categoria da Carta</label>
                    <input type="text" name="categoria_carta" class="form-control" value="{{ old('categoria_carta') }}">
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Contato de Emergência <span style="color: red;">*</span></label>
                    <input type="text" name="emergencia_contato" class="form-control @error('emergencia_contato') is-invalid @enderror" value="{{ old('emergencia_contato') }}" required>
                    @error('emergencia_contato') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Foto (URL)</label>
                    <input type="url" name="foto_url" class="form-control" value="{{ old('foto_url') }}" placeholder="https://...">
                </div>
                
                <div class="form-group">
                    <label class="checkbox">
                        <input type="checkbox" name="criar_usuario" value="1" {{ old('criar_usuario') ? 'checked' : '' }}>
                        <span>Criar usuário do sistema</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('colaboradores.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Salvar Colaborador
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const tipoSelect = document.getElementById('tipo');
    const cartaFields = document.getElementById('cartaFields');
    
    tipoSelect.addEventListener('change', function() {
        if(this.value === 'MOTORISTA') {
            cartaFields.style.display = 'block';
        } else {
            cartaFields.style.display = 'none';
        }
    });
</script>
@endpush