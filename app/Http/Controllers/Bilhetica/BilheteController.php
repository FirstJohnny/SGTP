<?php

namespace App\Http\Controllers\Bilhetica;

use App\Http\Controllers\Controller;
use App\Models\Tarifa;
use App\Models\Bilhete;
use App\Models\Escala;
use App\Models\PontoVenda;
use App\Models\ValidacaoBilhete;
use App\Models\LogAuditoria;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BilheteController extends Controller
{
    /**
     * Vender bilhete
     */
    public function vender(Request $request)
    {
        $validated = $request->validate([
            'tarifa_id' => 'required|exists:tarifa,id',
            'ponto_venda_id' => 'required|exists:ponto_venda,id',
            'forma_pagamento' => 'required|in:DINHEIRO,CARTAO,PIX,TRANSFERENCIA,OUTRO',
            'quantidade' => 'nullable|integer|min:1|max:10'
        ]);

        $quantidade = $validated['quantidade'] ?? 1;
        
        DB::beginTransaction();
        try {
            $tarifa = Tarifa::findOrFail($validated['tarifa_id']);
            
            if (!$tarifa->isVigente()) {
                return back()->with('error', 'Tarifa não está vigente');
            }
            
            // Verificar stock do ponto de venda
            $pontoVenda = PontoVenda::findOrFail($validated['ponto_venda_id']);
            if (!$pontoVenda->reduzirStock($quantidade)) {
                return back()->with('error', 'Stock de bilhetes insuficiente');
            }
            
            $bilhetes = [];
            for ($i = 0; $i < $quantidade; $i++) {
                $codigoBarras = $this->gerarCodigoBarras();
                
                $bilhete = Bilhete::create([
                    'codigo_barras' => $codigoBarras,
                    'tarifa_id' => $validated['tarifa_id'],
                    'valor_pago' => $tarifa->valor,
                    'data_venda' => now(),
                    'ponto_venda_id' => $validated['ponto_venda_id'],
                    'operador_id' => Auth::id(),
                    'status' => Bilhete::STATUS_VALIDO,
                    'data_validade' => now()->addDays(config('sgtp.bilhete_validade_dias', 30)),
                    'forma_pagamento' => $validated['forma_pagamento']
                ]);
                
                $bilhetes[] = $bilhete;
            }
            
            LogAuditoria::registrar(
                Auth::id(),
                'VENDER_BILHETE',
                'bilhete',
                $bilhetes[0]->id,
                null,
                ['quantidade' => $quantidade, 'valor_total' => $tarifa->valor * $quantidade]
            );
            
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'bilhetes' => $bilhetes,
                    'message' => $quantidade . ' bilhete(s) vendido(s) com sucesso!'
                ]);
            }
            
            return redirect()->route('bilhetes.show', $bilhetes[0])
                ->with('success', $quantidade . ' bilhete(s) vendido(s) com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Erro ao vender bilhete: ' . $e->getMessage());
        }
    }
    
    /**
     * Validar bilhete (embarque)
     */
    public function validar(Request $request)
    {
        $validated = $request->validate([
            'codigo_barras' => 'required|string',
            'veiculo_id' => 'required|exists:veiculo,id',
            'escala_id' => 'required|exists:escala,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'metodo' => 'required|in:QRCODE,BARRAS,NFC,MANUAL,OUTRO'
        ]);
        
        DB::beginTransaction();
        try {
            $bilhete = Bilhete::where('codigo_barras', $validated['codigo_barras'])->first();
            
            if (!$bilhete) {
                return $this->validationResponse($request, 'Bilhete não encontrado', 404);
            }
            
            if (!$bilhete->isValid()) {
                return $this->validationResponse($request, 'Bilhete inválido ou já utilizado', 400);
            }
            
            // Verificar se já foi validado
            $validacaoExistente = ValidacaoBilhete::where('bilhete_id', $bilhete->id)->first();
            if ($validacaoExistente) {
                return $this->validationResponse($request, 'Bilhete já foi utilizado', 400);
            }
            
            $validado = $bilhete->validar([
                'veiculo_id' => $validated['veiculo_id'],
                'escala_id' => $validated['escala_id'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'metodo' => $validated['metodo']
            ]);
            
            if (!$validado) {
                return $this->validationResponse($request, 'Erro ao validar bilhete', 500);
            }
            
            LogAuditoria::registrar(
                Auth::id(),
                'VALIDAR_BILHETE',
                'bilhete',
                $bilhete->id,
                null,
                ['metodo' => $validated['metodo'], 'veiculo_id' => $validated['veiculo_id']]
            );
            
            DB::commit();
            
            $response = [
                'success' => true,
                'message' => 'Bilhete validado com sucesso!',
                'tarifa' => $bilhete->tarifa->tipo_passageiro,
                'rota' => $bilhete->tarifa->rota->nome,
                'data_validade' => $bilhete->data_validade
            ];
            
            if ($request->ajax()) {
                return response()->json($response);
            }
            
            return back()->with('success', 'Bilhete validado com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->validationResponse($request, $e->getMessage(), 500);
        }
    }
    
    /**
     * Consultar bilhete por código
     */
    public function consultar($codigo, Request $request)
    {
        $bilhete = Bilhete::with(['tarifa.rota', 'pontoVenda', 'operador', 'validacao'])
            ->where('codigo_barras', $codigo)
            ->first();
            
        if (!$bilhete) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Bilhete não encontrado'], 404);
            }
            return back()->with('error', 'Bilhete não encontrado');
        }
        
        if ($request->ajax()) {
            return response()->json($bilhete);
        }
        
        return view('bilhetica.bilhetes.show', compact('bilhete'));
    }
    
    /**
     * Exibir formulário de venda
     */
    public function create()
    {
        $tarifas = Tarifa::with('rota')
            ->where('ativa', true)
            ->where('data_inicio', '<=', now())
            ->where(function($q) {
                $q->whereNull('data_fim')->orWhere('data_fim', '>=', now());
            })
            ->get();
            
        $pontosVenda = PontoVenda::where('ativo', true)->get();
        
        return view('bilhetica.bilhetes.vender', compact('tarifas', 'pontosVenda'));
    }
    
    /**
     * Exibir detalhes do bilhete
     */
    public function show(Bilhete $bilhete)
    {
        return view('bilhetica.bilhetes.show', compact('bilhete'));
    }
    
    /**
     * Gerar QR Code
     */
    public function qrCode(Bilhete $bilhete)
    {
        return view('bilhetica.bilhetes.qrcode', compact('bilhete'));
    }
    
    private function validationResponse($request, $message, $code = 400)
    {
        if ($request->ajax()) {
            return response()->json(['error' => $message], $code);
        }
        return back()->with('error', $message);
    }
    
    private function gerarCodigoBarras(): string
    {
        do {
            $codigo = 'SGT' . strtoupper(Str::random(6)) . date('ymd') . rand(10, 99);
        } while (Bilhete::where('codigo_barras', $codigo)->exists());
        
        return $codigo;
    }
    /**
 * Exibir formulário de validação
 */
public function validarForm()
{
    $veiculos = Veiculo::where('status', 'ATIVO')->get();
    $escalas = Escala::where('status', 'EM_ANDAMENTO')->get();
    
    return view('bilhetica.bilhetes.validar', compact('veiculos', 'escalas'));
}
}