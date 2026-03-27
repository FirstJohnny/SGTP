@extends('layouts.app')

@section('title', 'Nova Escala - SGTP')
@section('page-title', 'Criar Nova Escala')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-calendar-plus"></i> Dados da Escala</span>
        <a href="{{ route('escalas.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('escalas.store') }}" method="POST">
        @csrf
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Data <span style="color: red;">*</span></label>
                    <input type="date" name="data" class="form-control @error('data') is-invalid @enderror" value="{{ old('data', date('Y-m-d')) }}" required>
                    @error('data') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Veículo <span style="color: red;">*</span></label>
                    <select name="veiculo_id" class="form-control @error('veiculo_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($veiculos as $veiculo)
                        <option value="{{ $veiculo->id }}" {{ old('veiculo_id') == $veiculo->id ? 'selected' : '' }}>
                            {{ $veiculo->placa }} - {{ $veiculo->marca }} {{ $veiculo->modelo }}
                        </option>
                        @endforeach
                    </select>
                    @error('veiculo_id') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Motorista <span style="color: red;">*</span></label>
                    <select name="motorista_id" class="form-control @error('motorista_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($motoristas as $motorista)
                        <option value="{{ $motorista->id }}" {{ old('motorista_id') == $motorista->id ? 'selected' : '' }}>
                            {{ $motorista->nome_completo }} - {{ $motorista->categoria_carta }}
                        </option>
                        @endforeach
                    </select>
                    @error('motorista_id') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Cobrador (opcional)</label>
                    <select name="cobrador_id" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach($cobradores as $cobrador)
                        <option value="{{ $cobrador->id }}" {{ old('cobrador_id') == $cobrador->id ? 'selected' : '' }}>
                            {{ $cobrador->nome_completo }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Rota <span style="color: red;">*</span></label>
                    <select name="rota_id" class="form-control @error('rota_id') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        @foreach($rotas as $rota)
                        <option value="{{ $rota->id }}" {{ old('rota_id') == $rota->id ? 'selected' : '' }}>
                            {{ $rota->nome }} ({{ $rota->codigo }})
                        </option>
                        @endforeach
                    </select>
                    @error('rota_id') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hora Início <span style="color: red;">*</span></label>
                    <input type="time" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio') }}" required>
                    @error('hora_inicio') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Hora Fim <span style="color: red;">*</span></label>
                    <input type="time" name="hora_fim" class="form-control @error('hora_fim') is-invalid @enderror" value="{{ old('hora_fim') }}" required>
                    @error('hora_fim') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="3">{{ old('observacoes') }}</textarea>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('escalas.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Salvar Escala
            </button>
        </div>
    </form>
</div>
@endsection