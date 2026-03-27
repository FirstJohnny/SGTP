{{-- resources/views/bilhetica/bilhetes/vender.blade.php --}}
@extends('layouts.app')

@section('title', 'Vender Bilhete - SGTP')
@section('page-title', 'Venda de Bilhetes')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-ticket-alt"></i> Venda de Bilhetes</span>
        <a href="{{ route('dashboard') }}" class="btn-sm" style="background: var(--gray-500);">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    
    <form id="vendaForm" method="POST">
        @csrf
        
        <div class="row-cards" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div>
                <div class="form-group">
                    <label class="form-label">Tarifa <span style="color: red;">*</span></label>
                    <select name="tarifa_id" id="tarifa_id" class="form-control" required>
                        <option value="">Selecione a tarifa...</option>
                        @foreach($tarifas as $tarifa)
                        <option value="{{ $tarifa->id }}" data-valor="{{ $tarifa->valor }}">
                            {{ $tarifa->rota->nome }} - {{ $tarifa->tipo_passageiro }} (Kz {{ number_format($tarifa->valor, 2, ',', '.') }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Ponto de Venda <span style="color: red;">*</span></label>
                    <select name="ponto_venda_id" id="ponto_venda_id" class="form-control" required>
                        <option value="">Selecione o ponto de venda...</option>
                        @foreach($pontosVenda as $ponto)
                        <option value="{{ $ponto->id }}" data-stock="{{ $ponto->stock_bilhetes }}">
                            {{ $ponto->nome }} (Stock: {{ $ponto->stock_bilhetes }} bilhetes)
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Quantidade</label>
                    <input type="number" name="quantidade" id="quantidade" class="form-control" value="1" min="1" max="10">
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label class="form-label">Forma de Pagamento <span style="color: red;">*</span></label>
                    <select name="forma_pagamento" class="form-control" required>
                        <option value="DINHEIRO">Dinheiro</option>
                        <option value="CARTAO">Cartão</option>
                        <option value="PIX">PIX</option>
                        <option value="TRANSFERENCIA">Transferência</option>
                        <option value="OUTRO">Outro</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Valor Unitário</label>
                    <input type="text" id="valor_unitario" class="form-control" readonly value="Kz 0,00">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Valor Total</label>
                    <input type="text" id="valor_total" class="form-control" readonly value="Kz 0,00">
                </div>
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <a href="{{ route('dashboard') }}" class="btn" style="background: var(--gray-500);">Cancelar</a>
            <button type="submit" class="btn-accent" id="btnVender">
                <i class="fas fa-cash-register"></i> Vender Bilhete(s)
            </button>
        </div>
    </form>
</div>

<div id="bilhetesGerados" style="display: none; margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-check-circle"></i> Bilhetes Gerados</span>
        </div>
        <div id="bilhetesList" class="table-responsive"></div>
    </div>
</div>

<script>
    const tarifaSelect = document.getElementById('tarifa_id');
    const pontoSelect = document.getElementById('ponto_venda_id');
    const quantidadeInput = document.getElementById('quantidade');
    const valorUnitarioSpan = document.getElementById('valor_unitario');
    const valorTotalSpan = document.getElementById('valor_total');
    const btnVender = document.getElementById('btnVender');
    const vendaForm = document.getElementById('vendaForm');
    const bilhetesGeradosDiv = document.getElementById('bilhetesGerados');
    const bilhetesList = document.getElementById('bilhetesList');
    
    function atualizarValores() {
        const selectedOption = tarifaSelect.options[tarifaSelect.selectedIndex];
        const valor = selectedOption ? parseFloat(selectedOption.dataset.valor) || 0 : 0;
        const quantidade = parseInt(quantidadeInput.value) || 1;
        
        valorUnitarioSpan.value = 'Kz ' + valor.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        valorTotalSpan.value = 'Kz ' + (valor * quantidade).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    tarifaSelect.addEventListener('change', atualizarValores);
    quantidadeInput.addEventListener('input', atualizarValores);
    
    vendaForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(vendaForm);
        btnVender.disabled = true;
        btnVender.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
        
        fetch('{{ route("bilhetes.vender") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                exibirBilhetes(data.bilhetes);
                alert(data.message);
                vendaForm.reset();
                atualizarValores();
                
                // Atualizar stock do ponto de venda
                const pontoSelect = document.getElementById('ponto_venda_id');
                const stockAtual = parseInt(pontoSelect.options[pontoSelect.selectedIndex].dataset.stock);
                const quantidade = parseInt(document.getElementById('quantidade').value);
                const novoStock = stockAtual - quantidade;
                pontoSelect.options[pontoSelect.selectedIndex].dataset.stock = novoStock;
                pontoSelect.options[pontoSelect.selectedIndex].text = pontoSelect.options[pontoSelect.selectedIndex].text.replace(/\(Stock: \d+\)/, '(Stock: ' + novoStock + ')');
            } else {
                alert('Erro: ' + (data.error || data.message));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao processar venda. Tente novamente.');
        })
        .finally(() => {
            btnVender.disabled = false;
            btnVender.innerHTML = '<i class="fas fa-cash-register"></i> Vender Bilhete(s)';
        });
    });
    
    function exibirBilhetes(bilhetes) {
        if(!bilhetes || bilhetes.length === 0) return;
        
        let html = '<table class="data-table"><thead><tr><th>Código</th><th>Valor</th><th>Validade</th><th>QR Code</th></tr></thead><tbody>';
        bilhetes.forEach(b => {
            html += `<tr>
                        <td><strong>${b.codigo_barras}</strong></td>
                        <td>Kz ${parseFloat(b.valor_pago).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                        <td>${new Date(b.data_validade).toLocaleDateString('pt-BR')}</td>
                        <td><a href="/bilhetes/qr/${b.id}" target="_blank" class="btn-sm" style="background: var(--info);"><i class="fas fa-qrcode"></i> QR Code</a></td>
                    </tr>`;
        });
        html += '</tbody></table>';
        
        bilhetesList.innerHTML = html;
        bilhetesGeradosDiv.style.display = 'block';
        
        // Scroll para os bilhetes gerados
        bilhetesGeradosDiv.scrollIntoView({ behavior: 'smooth' });
    }
    
    atualizarValores();
</script>
@endsection