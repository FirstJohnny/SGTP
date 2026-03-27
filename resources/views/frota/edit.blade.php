@extends('layouts.app')

@section('title', 'Editar Veículo - SGTP')
@section('page-title', 'Editar Veículo: {{ $veiculo->placa }}')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-truck"></i> Editar Dados do Veículo</span>
        <a href="{{ route('frota.index') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form action="{{ route('frota.update', $veiculo) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Placa <span style="color: red;">*</span></label>
                    <input type="text" name="placa" class="form-control @error('placa') is-invalid @enderror" value="{{ old('placa', $veiculo->placa) }}" required>
                    @error('placa') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Chassi <span style="color: red;">*</span></label>
                    <input type="text" name="chassi" class="form-control @error('chassi') is-invalid @enderror" value="{{ old('chassi', $veiculo->chassi) }}" required>
                    @error('chassi') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Marca <span style="color: red;">*</span></label>
                    <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca', $veiculo->marca) }}" required>
                    @error('marca') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Modelo <span style="color: red;">*</span></label>
                    <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo', $veiculo->modelo) }}" required>
                    @error('modelo') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Ano de Fabricação <span style="color: red;">*</span></label>
                    <input type="number" name="ano_fabricado" class="form-control @error('ano_fabricado') is-invalid @enderror" value="{{ old('ano_fabricado', $veiculo->ano_fabricado) }}" required>
                    @error('ano_fabricado') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Cor <span style="color: red;">*</span></label>
                    <input type="text" name="cor" class="form-control @error('cor') is-invalid @enderror" value="{{ old('cor', $veiculo->cor) }}" required>
                    @error('cor') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Lotação <span style="color: red;">*</span></label>
                    <input type="number" name="lotacao" class="form-control @error('lotacao') is-invalid @enderror" value="{{ old('lotacao', $veiculo->lotacao) }}" required>
                    @error('lotacao') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tipo de Combustível <span style="color: red;">*</span></label>
                    <select name="tipo_combustivel" class="form-control @error('tipo_combustivel') is-invalid @enderror" required>
                        <option value="DIESEL" {{ old('tipo_combustivel', $veiculo->tipo_combustivel) == 'DIESEL' ? 'selected' : '' }}>Diesel</option>
                        <option value="GASOLINA" {{ old('tipo_combustivel', $veiculo->tipo_combustivel) == 'GASOLINA' ? 'selected' : '' }}>Gasolina</option>
                        <option value="ELETRICO" {{ old('tipo_combustivel', $veiculo->tipo_combustivel) == 'ELETRICO' ? 'selected' : '' }}>Elétrico</option>
                        <option value="HIBRIDO" {{ old('tipo_combustivel', $veiculo->tipo_combustivel) == 'HIBRIDO' ? 'selected' : '' }}>Híbrido</option>
                    </select>
                    @error('tipo_combustivel') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Consumo Médio (km/l)</label>
                    <input type="number" step="0.01" name="consumo_medio" class="form-control" value="{{ old('consumo_medio', $veiculo->consumo_medio) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">KM Atual <span style="color: red;">*</span></label>
                    <input type="number" name="km_atual" class="form-control @error('km_atual') is-invalid @enderror" value="{{ old('km_atual', $veiculo->km_atual) }}" required>
                    @error('km_atual') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Data de Aquisição <span style="color: red;">*</span></label>
                    <input type="date" name="data_aquisicao" class="form-control @error('data_aquisicao') is-invalid @enderror" value="{{ old('data_aquisicao', $veiculo->data_aquisicao) }}" required>
                    @error('data_aquisicao') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="ATIVO" {{ old('status', $veiculo->status) == 'ATIVO' ? 'selected' : '' }}>Ativo</option>
                        <option value="MANUTENCAO" {{ old('status', $veiculo->status) == 'MANUTENCAO' ? 'selected' : '' }}>Em Manutenção</option>
                        <option value="INATIVO" {{ old('status', $veiculo->status) == 'INATIVO' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Validade do Seguro <span style="color: red;">*</span></label>
                    <input type="date" name="seguro_validade" class="form-control @error('seguro_validade') is-invalid @enderror" value="{{ old('seguro_validade', $veiculo->seguro_validade) }}" required>
                    @error('seguro_validade') <small style="color: red;">{{ $message }}</small> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Última Inspeção</label>
                    <input type="date" name="ultima_inspecao" class="form-control" value="{{ old('ultima_inspecao', $veiculo->ultima_inspecao) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Próxima Inspeção</label>
                    <input type="date" name="proxima_inspecao" class="form-control" value="{{ old('proxima_inspecao', $veiculo->proxima_inspecao) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Observações</label>
                    <textarea name="observacoes" class="form-control" rows="3">{{ old('observacoes', $veiculo->observacoes) }}</textarea>
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('frota.index') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent">
                <i class="fas fa-save"></i> Atualizar Veículo
            </button>
        </div>
    </form>
</div>
@endsection