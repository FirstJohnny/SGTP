@extends('layouts.app')

@section('title', 'Novo Horário - SGTP')
@section('page-title', 'Adicionar Horário')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-clock"></i> Novo Horário para: {{ $rota->nome }}</span>
        <a href="{{ route('rotas.horarios.index', $rota) }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('rotas.horarios.store', $rota) }}" method="POST">
        @csrf
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Hora Partida <span style="color: red;">*</span></label>
                    <input type="time" name="hora_partida" class="form-control @error('hora_partida') is-invalid @enderror" value="{{ old('hora_partida') }}" required>
                    @error('hora_partida') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hora Chegada <span style="color: red;">*</span></label>
                    <input type="time" name="hora_chegada" class="form-control @error('hora_chegada') is-invalid @enderror" value="{{ old('hora_chegada') }}" required>
                    @error('hora_chegada') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Dias da Semana <span style="color: red;">*</span></label>
                    <select name="dias_semana" class="form-control @error('dias_semana') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="SEG,TER,QUA,QUI,SEX">Segunda a Sexta</option>
                        <option value="SAB,DOM">Finais de Semana</option>
                        <option value="SEG,TER,QUA,QUI,SEX,SAB,DOM">Todos os dias</option>
                        <option value="SEG">Segunda-feira</option>
                        <option value="TER">Terça-feira</option>
                        <option value="QUA">Quarta-feira</option>
                        <option value="QUI">Quinta-feira</option>
                        <option value="SEX">Sexta-feira</option>
                        <option value="SAB">Sábado</option>
                        <option value="DOM">Domingo</option>
                    </select>
                    @error('dias_semana') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo de Dia <span style="color: red;">*</span></label>
                    <select name="tipo_dia" class="form-control @error('tipo_dia') is-invalid @enderror" required>
                        <option value="NORMAL">Normal</option>
                        <option value="FERIADO">Feriado</option>
                        <option value="ESPECIAL">Especial</option>
                    </select>
                    @error('tipo_dia') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="checkbox">
                        <input type="checkbox" name="ativo" value="1" checked>
                        <span>Ativo</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('rotas.horarios.index', $rota) }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Salvar Horário
            </button>
        </div>
    </form>
</div>
@endsection