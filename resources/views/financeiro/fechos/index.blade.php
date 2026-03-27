@extends('layouts.app')

@section('title', 'Fechos de Caixa - SGTP')
@section('page-title', 'Histórico de Fechos de Caixa')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-cash-register"></i> Fechos de Caixa</span>
        <button onclick="fecharCaixa()" class="btn-accent">
            <i class="fas fa-lock"></i> Fechar Caixa
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                 <tr>
                    <th>Data/Hora</th>
                    <th>Operador</th>
                    <th>Valor Esperado</th>
                    <th>Valor Apurado</th>
                    <th>Diferença</th>
                    <th>Status</th>
                    <th>Observações</th>
                  </thead>
            <tbody>
                @forelse($fechamentos as $fecho)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($fecho->data_fecho)->format('d/m/Y H:i') }}</td>
                    <td>{{ $fecho->operador->name ?? 'N/A' }}</td>
                    <td>Kz {{ number_format($fecho->valor_esperado, 2, ',', '.') }}</td>
                    <td>Kz {{ number_format($fecho->valor_apurado, 2, ',', '.') }}</td>
                    <td class="{{ $fecho->diferenca >= 0 ? 'text-success' : 'text-danger' }}">
                        Kz {{ number_format($fecho->diferenca, 2, ',', '.') }}
                    </td>
                    <td><span class="status-badge status-ativo">{{ $fecho->status }}</span></td>
                    <td>{{ $fecho->observacoes ?? '--' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="7">Nenhum fechamento registrado.</td></tr>
                @endforelse
            </tbody>
          </table>
    </div>
    
    <div class="pagination">
        {{ $fechamentos->links() }}
    </div>
</div>

<script>
    function fecharCaixa() {
        const pontoVendaId = prompt('Informe o ID do Ponto de Venda:');
        const valorApurado = prompt('Informe o valor apurado em caixa:');
        const observacoes = prompt('Observações (opcional):');
        
        if(pontoVendaId && valorApurado) {
            fetch('{{ route("fecho-caixa.fechar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ponto_venda_id: pontoVendaId,
                    valor_apurado: parseFloat(valorApurado.replace(',', '.')),
                    observacoes: observacoes
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Caixa fechado com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            });
        }
    }
</script>
@endsection