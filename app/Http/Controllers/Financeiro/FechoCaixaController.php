<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\FechoCaixa;
use App\Models\Bilhete;
use App\Models\PontoVenda;
use App\Models\LogAuditoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class FechoCaixaController extends Controller
{

    /**
     * Fechar caixa do operador (RN11)
     */
    public function fechar(Request $request)
    {
        $validated = $request->validate([
            'ponto_venda_id' => 'required|exists:ponto_venda,id',
            'valor_apurado' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $pontoVenda = PontoVenda::findOrFail($validated['ponto_venda_id']);
            $dataFecho = Carbon::now();

            // Verificar se já existe fechamento para hoje
            $fechoExistente = FechoCaixa::where('operador_id', Auth::id())
                ->whereDate('data_fecho', $dataFecho->toDateString())
                ->exists();

            if ($fechoExistente) {
                return back()->with('error', 'Caixa já foi fechado hoje');
            }

            // Calcular valor esperado (vendas do dia)
            $valorEsperado = Bilhete::where('operador_id', Auth::id())
                ->whereDate('data_venda', $dataFecho->toDateString())
                ->sum('valor_pago');

            $fecho = FechoCaixa::create([
                'operador_id' => Auth::id(),
                'data_fecho' => $dataFecho,
                'valor_esperado' => $valorEsperado,
                'valor_apurado' => $validated['valor_apurado'],
                'observacoes' => $validated['observacoes'] ?? null,
                'status' => FechoCaixa::STATUS_FECHADO
            ]);

            $fecho->calcularDiferenca();

            LogAuditoria::registrar(
                Auth::id(),
                'FECHAR_CAIXA',
                'fecho_caixa',
                $fecho->id,
                null,
                $fecho->toArray()
            );

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Caixa fechado com sucesso! Diferença: ' . number_format($fecho->diferenca, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao fechar caixa: ' . $e->getMessage());
        }
    }

    /**
     * Histórico de fechamentos
     */
    public function historico(Request $request)
    {
        $query = FechoCaixa::with('operador');

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_fecho', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_fecho', '<=', $request->data_fim);
        }

        if ($request->filled('operador_id')) {
            $query->where('operador_id', $request->operador_id);
        }

        $fechamentos = $query->orderBy('data_fecho', 'desc')->paginate(20);

        return view('financeiro.fechos.index', compact('fechamentos'));
    }
}
