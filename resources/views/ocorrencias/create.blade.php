@extends('layouts.app')

@section('title', 'Registrar Ocorrência - SGTP')
@section('page-title', 'Registrar Nova Ocorrência')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-exclamation-triangle"></i> Dados da Ocorrência</span>
        <a href="{{ route('ocorrencias.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('ocorrencias.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Veículo <span style="color: red;">*</span></label>
                    <select name="veiculo_id" class="form-control @error('veiculo_id') is-invalid @enderror" required>
                        <option value="">Selecione o veículo...</option>
                        @foreach($veiculos as $veiculo)
                        <option value="{{ $veiculo->id }}" {{ old('veiculo_id') == $veiculo->id ? 'selected' : '' }}>
                            {{ $veiculo->placa }} - {{ $veiculo->marca }} {{ $veiculo->modelo }}
                        </option>
                        @endforeach
                    </select>
                    @error('veiculo_id') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Escala (opcional)</label>
                    <select name="escala_id" class="form-control">
                        <option value="">Selecione a escala...</option>
                        @foreach($escalas ?? [] as $escala)
                        <option value="{{ $escala->id }}" {{ old('escala_id') == $escala->id ? 'selected' : '' }}>
                            {{ $escala->data }} - {{ $escala->rota->nome ?? 'N/A' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo <span style="color: red;">*</span></label>
                    <select name="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="ACIDENTE" {{ old('tipo') == 'ACIDENTE' ? 'selected' : '' }}>Acidente</option>
                        <option value="ATRASO" {{ old('tipo') == 'ATRASO' ? 'selected' : '' }}>Atraso</option>
                        <option value="FALHA_MECANICA" {{ old('tipo') == 'FALHA_MECANICA' ? 'selected' : '' }}>Falha Mecânica</option>
                        <option value="ASSALTO" {{ old('tipo') == 'ASSALTO' ? 'selected' : '' }}>Assalto</option>
                        <option value="OUTRO" {{ old('tipo') == 'OUTRO' ? 'selected' : '' }}>Outro</option>
                    </select>
                    @error('tipo') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Gravidade <span style="color: red;">*</span></label>
                    <select name="gravidade" class="form-control @error('gravidade') is-invalid @enderror" required>
                        <option value="">Selecione...</option>
                        <option value="LEVE" {{ old('gravidade') == 'LEVE' ? 'selected' : '' }}>Leve</option>
                        <option value="MEDIA" {{ old('gravidade') == 'MEDIA' ? 'selected' : '' }}>Média</option>
                        <option value="GRAVE" {{ old('gravidade') == 'GRAVE' ? 'selected' : '' }}>Grave</option>
                        <option value="CRITICA" {{ old('gravidade') == 'CRITICA' ? 'selected' : '' }}>Crítica</option>
                    </select>
                    @error('gravidade') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Data e Hora da Ocorrência <span style="color: red;">*</span></label>
                    <input type="datetime-local" name="data_ocorrencia" class="form-control @error('data_ocorrencia') is-invalid @enderror" value="{{ old('data_ocorrencia', now()->format('Y-m-d\TH:i')) }}" required>
                    @error('data_ocorrencia') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Latitude (opcional)</label>
                    <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="-8.838333">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Longitude (opcional)</label>
                    <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="13.234444">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Fotos (opcional)</label>
                    <input type="file" name="fotos[]" class="form-control" multiple accept="image/*">
                    <small>Você pode selecionar múltiplas imagens</small>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Descrição Detalhada <span style="color: red;">*</span></label>
            <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="5" required>{{ old('descricao') }}</textarea>
            @error('descricao') <small style="color: red;">{{ $message }}</small> @enderror
        </div>
        
        <div class="form-group">
            <label class="form-label">Status <span style="color: red;">*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="ABERTA" {{ old('status') == 'ABERTA' ? 'selected' : '' }}>Aberta</option>
                <option value="EM_ANALISE" {{ old('status') == 'EM_ANALISE' ? 'selected' : '' }}>Em Análise</option>
                <option value="RESOLVIDA" {{ old('status') == 'RESOLVIDA' ? 'selected' : '' }}>Resolvida</option>
                <option value="CANCELADA" {{ old('status') == 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
            </select>
            @error('status') <small style="color: red;">{{ $message }}</small> @enderror
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('ocorrencias.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Registrar Ocorrência
            </button>
        </div>
    </form>
</div>
@endsection