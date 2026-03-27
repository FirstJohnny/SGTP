@extends('layouts.app')

@section('title', 'Editar Perfil - SGTP')
@section('page-title', 'Editar Perfil: {{ $perfil->nome }}')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-shield-alt"></i> Editar Perfil</span>
        <a href="{{ route('admin.perfis.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('admin.perfis.update', $perfil) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label class="form-label">Nome <span style="color: red;">*</span></label>
            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $perfil->nome) }}" required>
            @error('nome') <small style="color: red;">{{ $message }}</small> @enderror
        </div>
        
        <div class="form-group">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $perfil->descricao) }}</textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Permissões</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px;">
                @foreach($permissoesPorModulo as $modulo => $permissoes)
                <div>
                    <strong>{{ $modulo }}</strong>
                    @foreach($permissoes as $permissao)
                    <div>
                        <label class="checkbox">
                            <input type="checkbox" name="permissoes[]" value="{{ $permissao->id }}" 
                                {{ in_array($permissao->id, $permissoesSelecionadas) ? 'checked' : '' }}>
                            <span>{{ $permissao->nome }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('admin.perfis.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Atualizar Perfil
            </button>
        </div>
    </form>
</div>
@endsection