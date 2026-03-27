@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gestão de Veículos</h2>
        <a href="{{ route('frota.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
            + Novo Veículo
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Placa / Modelo
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Lotação
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($veiculos ?? [] as $veiculo)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="flex items-center">
                            <div class="ml-3">
                                <p class="text-gray-900 whitespace-no-wrap font-bold">{{ $veiculo->placa }}</p>
                                <p class="text-gray-600 whitespace-no-wrap">{{ $veiculo->modelo }} ({{ $veiculo->ano }})</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $veiculo->status === 'ATIVO' ? 'text-green-900' : 'text-red-900' }}">
                            <span aria-hidden="true" class="absolute inset-0 {{ $veiculo->status === 'ATIVO' ? 'bg-green-200' : 'bg-red-200' }} opacity-50 rounded-full"></span>
                            <span class="relative">{{ $veiculo->status }}</span>
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $veiculo->lotacao }} Lugares</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="{{ route('frota.edit', $veiculo->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                        <a href="{{ route('frota.show', $veiculo->id) }}" class="text-gray-600 hover:text-gray-900">Detalhes</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                        Nenhum veículo cadastrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $veiculos->links() ?? '' }}
        </div>
    </div>
@endsection