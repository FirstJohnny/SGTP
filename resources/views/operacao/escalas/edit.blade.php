@extends('layouts.app')

@section('title', 'Editar Escala - SGTP')
@section('page-title', 'Editar Escala')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-calendar-edit"></i> Editar Escala</span>
        <a href="{{ route('escalas.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('escalas.update', $escala) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Data</label>
                    <input type="date" class="form-control" value="{{ $escala->data }}" disabled>
                    <small>Data não pode ser alterada</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="PENDENTE" {{ $escala->status == 'PENDENTE' ? 'selected' : '' }}>Pendente</option>
                        <option value="EM_ANDAMENTO" {{ $escala->status == 'EM_ANDAMENTO' ? 'selected' : '' }}>Em Andamento</option>
                        <option value="FINALIZADA" {{ $escala->status == 'FINALIZADA' ? 'selected' : '' }}>Finalizada</option>
                        <option value="CANCELADA" {{ $escala->status == 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('status') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">KM Inicial</label>
                    <input type="number" name="km_inicial" class="form-control" value="{{ old('km_inicial', $escala->km_inicial) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">KM Final</label>
                    <input type="number" name="km_final" class="form-control" value="{{ old('km_final', $escala->km_final) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hora Início Real</label>
                    <input type="time" name="hora_inicio_real" class="form-control" value="{{ old('hora_inicio_real', $escala->hora_inicio_real) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hora Fim Real</label>
                    <input type="time" name="hora_fim_real" class="form-control" value="{{ old('hora_fim_real', $escala->hora_fim_real) }}">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="3">{{ old('observacoes', $escala->observacoes) }}</textarea>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('escalas.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Atualizar Escala
            </button>
        </div>
    </form>
</div>
@endsection