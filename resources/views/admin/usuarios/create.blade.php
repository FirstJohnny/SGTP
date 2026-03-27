@extends('layouts.app')

@section('title', 'Novo Usuário - SGTP')
@section('page-title', 'Criar Novo Usuário')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-user-plus"></i> Dados do Usuário</span>
        <a href="{{ route('admin.usuarios.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('admin.usuarios.store') }}" method="POST">
        @csrf
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Nome <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">E-mail <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">BI</label>
                    <input type="text" name="bi" class="form-control" value="{{ old('bi') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone') }}">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label class="form-label">Tipo de Usuário <span style="color: red;">*</span></label>
                    <select name="tipo_usuario" class="form-control @error('tipo_usuario') is-invalid @enderror" required>
                        <option value="ADMIN">Administrador</option>
                        <option value="GESTOR_OPERACOES">Gestor Operações</option>
                        <option value="GESTOR_FROTA">Gestor Frota</option>
                        <option value="FISCAL">Fiscal</option>
                        <option value="OPERADOR_BILHETICA">Operador Bilhética</option>
                        <option value="FINANCEIRO">Financeiro</option>
                    </select>
                    @error('tipo_usuario') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="ATIVO">Ativo</option>
                        <option value="INATIVO">Inativo</option>
                        <option value="BLOQUEADO">Bloqueado</option>
                    </select>
                    @error('status') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Perfil de Acesso</label>
                    <select name="perfil_acesso_id" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach($perfis as $perfil)
                        <option value="{{ $perfil->id }}">{{ $perfil->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <span><i class="fas fa-lock"></i> Senha</span>
            </div>
            <div class="row-cards">
                <div class="form-group">
                    <label class="form-label">Senha <span style="color: red;">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar Senha <span style="color: red;">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('admin.usuarios.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Salvar Usuário
            </button>
        </div>
    </form>
</div>
@endsection