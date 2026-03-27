{{-- resources/views/bilhetica/bilhetes/validar.blade.php --}}
@extends('layouts.app')

@section('title', 'Validar Bilhete - SGTP')
@section('page-title', 'Validação de Bilhetes')

@section('content')
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-check-circle"></i> Validação de Bilhete</span>
    </div>
    
    <div class="row-cards">
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-camera"></i> Leitura de QR Code</span>
            </div>
            <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
            <div style="text-align: center; margin-top: 16px;">
                <button class="btn" onclick="document.getElementById('codigoInput').focus()">
                    <i class="fas fa-keyboard"></i> Digitar Código Manualmente
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-edit"></i> Validação Manual</span>
            </div>
            <div class="form-group">
                <label class="form-label">Código do Bilhete</label>
                <input type="text" id="codigoInput" class="form-control" placeholder="Digite ou cole o código do bilhete">
            </div>
            <button class="btn-accent" onclick="validarBilhete()">
                <i class="fas fa-check"></i> Validar Bilhete
            </button>
        </div>
    </div>
</div>

<div id="resultadoValidacao" style="display: none; margin-top: 24px;">
    <div class="card" id="resultadoSuccess" style="border-left: 4px solid var(--success);">
        <div class="card-header">
            <span><i class="fas fa-check-circle" style="color: var(--success);"></i> Bilhete Válido</span>
        </div>
        <div id="successContent"></div>
    </div>
    <div class="card" id="resultadoError" style="border-left: 4px solid var(--danger); display: none;">
        <div class="card-header">
            <span><i class="fas fa-times-circle" style="color: var(--danger);"></i> Bilhete Inválido</span>
        </div>
        <div id="errorContent"></div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    
    function startScanner() {
        const readerElement = document.getElementById('reader');
        if (html5QrCode) {
            html5QrCode.stop();
        }
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanError);
    }
    
    function onScanSuccess(decodedText, decodedResult) {
        document.getElementById('codigoInput').value = decodedText;
        validarBilhete();
    }
    
    function onScanError(errorMessage) {
        // console.log(errorMessage);
    }
    
    function validarBilhete() {
        const codigo = document.getElementById('codigoInput').value.trim();
        if(!codigo) {
            alert('Digite o código do bilhete');
            return;
        }
        
        const veiculoId = prompt('Digite o ID do veículo:', '1');
        const escalaId = prompt('Digite o ID da escala:', '1');
        
        if(!veiculoId || !escalaId) {
            alert('Veículo e escala são obrigatórios');
            return;
        }
        
        fetch('{{ route("bilhetes.validar") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codigo_barras: codigo,
                veiculo_id: veiculoId,
                escala_id: escalaId,
                metodo: 'MANUAL'
            })
        })
        .then(response => response.json())
        .then(data => {
            const resultadoDiv = document.getElementById('resultadoValidacao');
            const successDiv = document.getElementById('resultadoSuccess');
            const errorDiv = document.getElementById('resultadoError');
            
            resultadoDiv.style.display = 'block';
            
            if(data.success) {
                successDiv.style.display = 'block';
                errorDiv.style.display = 'none';
                document.getElementById('successContent').innerHTML = `
                    <p><strong>✅ ${data.message}</strong></p>
                    <p>Tarifa: ${data.tarifa}</p>
                    <p>Rota: ${data.rota}</p>
                    <p>Validade: ${new Date(data.data_validade).toLocaleDateString('pt-BR')}</p>
                `;
                document.getElementById('codigoInput').value = '';
            } else {
                successDiv.style.display = 'none';
                errorDiv.style.display = 'block';
                document.getElementById('errorContent').innerHTML = `<p><strong>❌ ${data.error || data.message}</strong></p>`;
            }
            
            setTimeout(() => {
                resultadoDiv.style.display = 'none';
            }, 5000);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao validar bilhete');
        });
    }
    
    // Iniciar scanner quando a página carregar
    startScanner();
    
    // Permitir Enter no campo de código
    document.getElementById('codigoInput').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            validarBilhete();
        }
    });
</script>
@endsection