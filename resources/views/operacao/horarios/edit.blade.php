@extends('layouts.app')

@section('title', 'Editar Horário - SGTP')
@section('page-title', 'Editar Horário')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-clock"></i> Editar Horário</span>
        <a href="{{ route('rotas.horarios.index', $rota) }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('rotas.horarios.update', [$rota, $horario]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Hora Partida <span style="color: red;">*</span></label>
                    <input type="time" name="hora_partida" class="form-control @error('hora_partida') is-invalid @enderror" value="{{ old('hora_partida', $horario->hora_partida) }}" required>
                    @error('hora_partida') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hora Chegada <span style="color: red;">*</span></label>
                    <input type="time" name="hora_chegada" class="form-control @error('hora_chegada') is-invalid @enderror" value="{{ old('hora_chegada', $horario->hora_chegada) }}" required>
                    @error('hora_chegada') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Dias da Semana <span style="color: red;">*</span></label>
                    <input type="text" name="dias_semana" class="form-control @error('dias_semana') is-invalid @enderror" value="{{ old('dias_semana', $horario->dias_semana) }}" required>
                    <small>Ex: SEG,TER,QUA,QUI,SEX ou SAB,DOM</small>
                    @error('dias_semana') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo de Dia <span style="color: red;">*</span></label>
                    <select name="tipo_dia" class="form-control @error('tipo_dia') is-invalid @enderror" required>
                        <option value="NORMAL" {{ $horario->tipo_dia == 'NORMAL' ? 'selected' : '' }}>Normal</option>
                        <option value="FERIADO" {{ $horario->tipo_dia == 'FERIADO' ? 'selected' : '' }}>Feriado</option>
                        <option value="ESPECIAL" {{ $horario->tipo_dia == 'ESPECIAL' ? 'selected' : '' }}>Especial</option>
                    </select>
                    @error('tipo_dia') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="checkbox">
                        <input type="checkbox" name="ativo" value="1" {{ $horario->ativo ? 'checked' : '' }}>
                        <span>Ativo</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('rotas.horarios.index', $rota) }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Atualizar Horário
            </button>
        </div>
    </form>
</div>
@endsection