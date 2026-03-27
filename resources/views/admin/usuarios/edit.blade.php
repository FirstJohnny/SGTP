@extends('layouts.app')

@section('title', 'Editar Usuário - SGTP')
@section('page-title', 'Editar Usuário: {{ $usuario->name }}')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-user-edit"></i> Editar Usuário</span>
        <a href="{{ route('admin.usuarios.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Nome <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $usuario->name) }}" required>
                    @error('name') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">E-mail <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}" required>
                    @error('email') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">BI</label>
                    <input type="text" name="bi" class="form-control" value="{{ old('bi', $usuario->bi) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $usuario->telefone) }}">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label class="form-label">Tipo de Usuário <span style="color: red;">*</span></label>
                    <select name="tipo_usuario" class="form-control @error('tipo_usuario') is-invalid @enderror" required>
                        <option value="ADMIN" {{ $usuario->tipo_usuario == 'ADMIN' ? 'selected' : '' }}>Administrador</option>
                        <option value="GESTOR_OPERACOES" {{ $usuario->tipo_usuario == 'GESTOR_OPERACOES' ? 'selected' : '' }}>Gestor Operações</option>
                        <option value="GESTOR_FROTA" {{ $usuario->tipo_usuario == 'GESTOR_FROTA' ? 'selected' : '' }}>Gestor Frota</option>
                        <option value="FISCAL" {{ $usuario->tipo_usuario == 'FISCAL' ? 'selected' : '' }}>Fiscal</option>
                        <option value="OPERADOR_BILHETICA" {{ $usuario->tipo_usuario == 'OPERADOR_BILHETICA' ? 'selected' : '' }}>Operador Bilhética</option>
                        <option value="FINANCEIRO" {{ $usuario->tipo_usuario == 'FINANCEIRO' ? 'selected' : '' }}>Financeiro</option>
                    </select>
                    @error('tipo_usuario') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="ATIVO" {{ $usuario->status == 'ATIVO' ? 'selected' : '' }}>Ativo</option>
                        <option value="INATIVO" {{ $usuario->status == 'INATIVO' ? 'selected' : '' }}>Inativo</option>
                        <option value="BLOQUEADO" {{ $usuario->status == 'BLOQUEADO' ? 'selected' : '' }}>Bloqueado</option>
                    </select>
                    @error('status') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Perfil de Acesso</label>
                    <select name="perfil_acesso_id" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach($perfis as $perfil)
                        <option value="{{ $perfil->id }}" {{ $usuario->perfil_acesso_id == $perfil->id ? 'selected' : '' }}>{{ $perfil->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <span><i class="fas fa-lock"></i> Alterar Senha (opcional)</span>
            </div>
            <div class="row-cards">
                <div class="form-group">
                    <label class="form-label">Nova Senha</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('admin.usuarios.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Atualizar Usuário
            </button>
        </div>
    </form>
</div>
@endsection