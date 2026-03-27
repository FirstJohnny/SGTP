@extends('layouts.app')

@section('title', 'Venda de Bilhetes - SGTP')
@section('page-title', 'Venda de Bilhetes')

@section('content')
<div class="row-cards">
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-ticket-alt"></i> Nova Venda</span>
        </div>
        
        <form id="vendaForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Rota <span style="color: red;">*</span></label>
                <select id="rota_id" class="form-control" required>
                    <option value="">Selecione a rota...</option>
                    @foreach($rotas as $rota)
                    <option value="{{ $rota->id }}">{{ $rota->nome }} ({{ $rota->codigo }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tipo de Passageiro <span style="color: red;">*</span></label>
                <select id="tipo_passageiro" class="form-control" required>
                    <option value="">Selecione...</option>
                    <option value="ADULTO">Adulto</option>
                    <option value="ESTUDANTE">Estudante</option>
                    <option value="IDOSO">Idoso</option>
                    <option value="OUTRO">Outro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Ponto de Venda <span style="color: red;">*</span></label>
                <select id="ponto_venda_id" class="form-control" required>
                    <option value="">Selecione o ponto de venda...</option>
                    @foreach($pontosVenda as $ponto)
                    <option value="{{ $ponto->id }}">{{ $ponto->nome }} (Stock: {{ $ponto->stock_bilhetes }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Forma de Pagamento <span style="color: red;">*</span></label>
                <select id="forma_pagamento" class="form-control" required>
                    <option value="DINHEIRO">Dinheiro</option>
                    <option value="CARTAO">Cartão</option>
                    <option value="PIX">PIX</option>
                    <option value="TRANSFERENCIA">Transferência</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Valor Pago (Kz) <span style="color: red;">*</span></label>
                <input type="number" step="0.01" id="valor_pago" class="form-control" placeholder="Valor pago pelo passageiro" required>
            </div>
            
            <div id="tarifaInfo" class="alert alert-info" style="display: none;">
                <i class="fas fa-info-circle"></i> Tarifa: <strong id="tarifaValor">Kz 0,00</strong>
            </div>
            
            <button type="submit" class="btn-accent" style="width: 100%;">
                <i class="fas fa-ticket-alt"></i> Vender Bilhete
            </button>
        </form>
    </div>
    
    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-history"></i> Últimas Vendas</span>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Rota</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Status</th>
                     </thead>
                <tbody id="ultimasVendas">
                    <tr><td colspan="6" style="text-align: center;">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span><i class="fas fa-qrcode"></i> Validar Bilhete</span>
    </div>
    <div class="row-cards">
        <div class="form-group">
            <label class="form-label">Código do Bilhete</label>
            <div style="display: flex; gap: 12px;">
                <input type="text" id="codigoValidacao" class="form-control" placeholder="Digite ou escaneie o código do bilhete">
                <button onclick="validarBilhete()" class="btn-accent">
                    <i class="fas fa-check-circle"></i> Validar
                </button>
            </div>
        </div>
    </div>
    <div id="validacaoResultado" style="display: none; margin-top: 16px;"></div>
</div>

<script>
    // Buscar tarifa quando selecionar rota e tipo
    const rotaSelect = document.getElementById('rota_id');
    const tipoSelect = document.getElementById('tipo_passageiro');
    const tarifaInfo = document.getElementById('tarifaInfo');
    const tarifaValor = document.getElementById('tarifaValor');
    
    async function buscarTarifa() {
        const rotaId = rotaSelect.value;
        const tipo = tipoSelect.value;
        
        if (rotaId && tipo) {
            try {
                const response = await fetch(`/api/public/tarifas?rota_id=${rotaId}&tipo_passageiro=${tipo}`);
                const tarifas = await response.json();
                
                if (tarifas.length > 0) {
                    const tarifa = tarifas[0];
                    tarifaValor.textContent = `Kz ${tarifa.valor.toFixed(2).replace('.', ',')}`;
                    tarifaInfo.style.display = 'block';
                    document.getElementById('valor_pago').value = tarifa.valor;
                } else {
                    tarifaInfo.style.display = 'none';
                }
            } catch (error) {
                console.error('Erro ao buscar tarifa:', error);
            }
        } else {
            tarifaInfo.style.display = 'none';
        }
    }
    
    rotaSelect.addEventListener('change', buscarTarifa);
    tipoSelect.addEventListener('change', buscarTarifa);
    
    // Vender bilhete
    document.getElementById('vendaForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const dados = {
            tarifa_id: null, // Vamos buscar a tarifa
            rota_id: rotaSelect.value,
            tipo_passageiro: tipoSelect.value,
            ponto_venda_id: document.getElementById('ponto_venda_id').value,
            forma_pagamento: document.getElementById('forma_pagamento').value,
            valor_pago: parseFloat(document.getElementById('valor_pago').value)
        };
        
        // Buscar tarifa primeiro
        try {
            const tarifaResponse = await fetch(`/api/public/tarifas?rota_id=${dados.rota_id}&tipo_passageiro=${dados.tipo_passageiro}`);
            const tarifas = await tarifaResponse.json();
            
            if (tarifas.length === 0) {
                alert('Nenhuma tarifa encontrada para esta rota e tipo de passageiro');
                return;
            }
            
            dados.tarifa_id = tarifas[0].id;
            
            // Vender bilhete
            const response = await fetch('/bilhetes/vender', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(`Bilhete vendido com sucesso!\nCódigo: ${result.codigo_barras}`);
                carregarUltimasVendas();
                document.getElementById('vendaForm').reset();
                tarifaInfo.style.display = 'none';
            } else {
                alert('Erro: ' + result.error);
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao processar venda');
        }
    });
    
    // Carregar últimas vendas
    async function carregarUltimasVendas() {
        try {
            const response = await fetch('/api/ultimas-vendas');
            const vendas = await response.json();
            const tbody = document.getElementById('ultimasVendas');
            
            if (vendas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Nenhuma venda registrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = vendas.map(venda => `
                <tr>
                    <td><strong>${venda.codigo_barras}</strong></td>
                    <td>${venda.rota_nome}</td>
                    <td>${venda.tipo_passageiro}</td>
                    <td>Kz ${venda.valor_pago.toFixed(2).replace('.', ',')}</td>
                    <td>${new Date(venda.data_venda).toLocaleString()}</td>
                    <td><span class="status-badge status-ativo">${venda.status}</span></td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Erro:', error);
        }
    }
    
    // Validar bilhete
    async function validarBilhete() {
        const codigo = document.getElementById('codigoValidacao').value.trim();
        const resultadoDiv = document.getElementById('validacaoResultado');
        
        if (!codigo) {
            alert('Digite o código do bilhete');
            return;
        }
        
        try {
            const response = await fetch('/bilhetes/validar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    codigo_barras: codigo,
                    veiculo_id: {{ $veiculoAtual ?? 1 }},
                    escala_id: {{ $escalaAtual ?? 1 }},
                    metodo: 'MANUAL'
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                resultadoDiv.style.display = 'block';
                resultadoDiv.className = 'alert alert-success';
                resultadoDiv.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    <strong>Bilhete Válido!</strong><br>
                    Rota: ${result.rota}<br>
                    Tipo: ${result.tarifa}<br>
                    Data Validade: ${new Date(result.data_validade).toLocaleDateString()}
                `;
                carregarUltimasVendas();
            } else {
                resultadoDiv.style.display = 'block';
                resultadoDiv.className = 'alert alert-danger';
                resultadoDiv.innerHTML = `<i class="fas fa-times-circle"></i> ${result.error}`;
            }
            
            setTimeout(() => {
                resultadoDiv.style.display = 'none';
            }, 5000);
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao validar bilhete');
        }
    }
    
    // Carregar últimas vendas ao iniciar
    carregarUltimasVendas();
</script>
@endsection