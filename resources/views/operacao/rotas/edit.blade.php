@extends('layouts.app')

@section('title', 'Editar Rota - SGTP')
@section('page-title', 'Editar Rota: {{ $rota->nome }}')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-map-marked-alt"></i> Editar Rota</span>
        <a href="{{ route('rotas.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('rotas.update', $rota) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Nome da Rota <span style="color: red;">*</span></label>
                    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $rota->nome) }}" required>
                    @error('nome') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Código <span style="color: red;">*</span></label>
                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" value="{{ old('codigo', $rota->codigo) }}" required>
                    @error('codigo') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo <span style="color: red;">*</span></label>
                    <select name="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                        <option value="URBANA" {{ old('tipo', $rota->tipo) == 'URBANA' ? 'selected' : '' }}>Urbana</option>
                        <option value="INTERMUNICIPAL" {{ old('tipo', $rota->tipo) == 'INTERMUNICIPAL' ? 'selected' : '' }}>Intermunicipal</option>
                        <option value="RODOVIARIA" {{ old('tipo', $rota->tipo) == 'RODOVIARIA' ? 'selected' : '' }}>Rodoviária</option>
                        <option value="ESCOLAR" {{ old('tipo', $rota->tipo) == 'ESCOLAR' ? 'selected' : '' }}>Escolar</option>
                    </select>
                    @error('tipo') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Distância Total (km) <span style="color: red;">*</span></label>
                    <input type="number" step="0.1" name="distancia_total" class="form-control @error('distancia_total') is-invalid @enderror" value="{{ old('distancia_total', $rota->distancia_total) }}" required>
                    @error('distancia_total') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tempo Estimado (minutos)</label>
                    <input type="number" name="tempo_estimado" class="form-control" value="{{ old('tempo_estimado', $rota->tempo_estimado) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Empresa Responsável</label>
                    <input type="text" name="empresa_responsavel" class="form-control" value="{{ old('empresa_responsavel', $rota->empresa_responsavel) }}">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $rota->descricao) }}</textarea>
        </div>
        
        <div class="form-group">
            <label class="checkbox">
                <input type="checkbox" name="ativa" value="1" {{ $rota->ativa ? 'checked' : '' }}>
                <span>Rota Ativa</span>
            </label>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('rotas.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Atualizar Rota
            </button>
        </div>
    </form>
</div>
@endsection