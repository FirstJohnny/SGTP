@extends('layouts.app')

@section('title', 'Meu Perfil - SGTP')
@section('page-title', 'Meu Perfil')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-user-circle"></i> Dados Pessoais</span>
        <a href="{{ route('dashboard') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Nome Completo <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name) }}" required>
                    @error('name') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">E-mail <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" required>
                    @error('email') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone', Auth::user()->telefone) }}">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label class="form-label">BI</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->bi }}" disabled>
                    <small>Este campo não pode ser alterado.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de Usuário</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->tipo_usuario }}" disabled>
                    <small>Entre em contato com o administrador para alterar.</small>
                </div>
            </div>
        </div>
        
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <span><i class="fas fa-lock"></i> Alterar Senha</span>
            </div>
            <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <div class="form-group">
                    <label class="form-label">Senha Atual</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                    @error('current_password') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nova Senha</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('dashboard') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection