@extends('layouts.app')

@section('title', 'Horários - SGTP')
@section('page-title', 'Gestão de Horários')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-clock"></i> Horários da Rota: {{ $rota->nome }} ({{ $rota->codigo }})</span>
        @can('gerir_operacoes')
        <a href="{{ route('rotas.horarios.create', $rota) }}" class="btn-accent">
            <i class="fas fa-plus"></i> Novo Horário
        </a>
        @endcan
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                  <tr>
                    <th>Hora Partida</th>
                    <th>Hora Chegada</th>
                    <th>Dias da Semana</th>
                    <th>Tipo de Dia</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
            </thead>
            <tbody>
                @forelse($horarios as $horario)
                  <tr>
                    <td><strong>{{ $horario->hora_partida }}</strong></td>
                    <td>{{ $horario->hora_chegada }}</td>
                    <td>{{ $horario->dias_semana }}</td>
                    <td>{{ $horario->tipo_dia }}</td>
                    <td>
                        @if($horario->ativo)
                            <span class="status-badge status-ativo">Ativo</span>
                        @else
                            <span class="status-badge status-inativo">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('rotas.horarios.edit', [$rota, $horario]) }}" class="btn-sm" style="background: var(--warning);">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('rotas.horarios.destroy', [$rota, $horario]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm" style="background: var(--danger);" onclick="return confirm('Tem certeza?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" style="text-align: center;">Nenhum horário cadastrado para esta rota.</td>
                  </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="{{ route('rotas.index') }}" class="btn" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>
@endsection