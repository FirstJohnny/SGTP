@extends('layouts.app')

@section('title', 'Perfis de Acesso - SGTP')
@section('page-title', 'Gestão de Perfis de Acesso')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-shield-alt"></i> Perfis de Acesso</span>
        <a href="{{ route('admin.perfis.create') }}" class="btn-accent">
            <i class="fas fa-plus"></i> Novo Perfil
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Permissões</th>
                    <th>Usuários</th>
                    <th>Ações</th>
                  </thead>
            <tbody>
                @forelse($perfis as $perfil)
                  <tr>
                    <td><strong>{{ $perfil->nome }}</strong></td>
                    <td>{{ $perfil->descricao ?? '--' }}</td>
                    <td>{{ $perfil->permissoes->count() }}</td>
                    <td>{{ $perfil->usuarios->count() }}</td>
                    <td>
                        <a href="{{ route('admin.perfis.edit', $perfil) }}" class="btn-sm" style="background: var(--warning);">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($perfil->usuarios->count() == 0)
                        <form action="{{ route('admin.perfis.destroy', $perfil) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm" style="background: var(--danger);" onclick="return confirm('Tem certeza?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5">Nenhum perfil cadastrado.</td></tr>
                @endforelse
            </tbody>
          </table>
    </div>
</div>
@endsection