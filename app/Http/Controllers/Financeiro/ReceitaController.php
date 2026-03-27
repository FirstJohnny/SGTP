<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Receita;
use App\Models\Bilhete;
use App\Models\ValidacaoBilhete;
use App\Models\LogAuditoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class ReceitaController extends Controller
{

    /**
     * Consolidar receitas diárias
     */
    public function consolidar(Request $request)
    {
        $data = $request->filled('data') ? Carbon::parse($request->data) : Carbon::yesterday();

        DB::beginTransaction();
        try {
            // Verificar se já foi consolidado
            if (Receita::whereDate('data', $data)->where('consolidado', true)->exists()) {
                return back()->with('warning', 'Receitas já consolidadas para esta data');
            }

            // Calcular receitas de bilhetes validados
            $valorBilhetes = ValidacaoBilhete::whereDate('timestamp', $data)
                ->join('bilhete', 'validacao_bilhete.bilhete_id', '=', 'bilhete.id')
                ->sum('bilhete.valor_pago');

            // Criar registro de receita
            $receita = Receita::create([
                'data' => $data,
                'valor_total' => $valorBilhetes,
                'origem' => Receita::ORIGEM_BILHETE,
                'descricao' => "Consolidação automática de bilhetes validados em {$data->format('d/m/Y')}",
                'consolidado' => true
            ]);

            LogAuditoria::registrar(
                Auth::id(),
                'CONSOLIDAR_RECEITA',
                'receita',
                $receita->id,
                null,
                $receita->toArray()
            );

            DB::commit();

            return redirect()->route('receitas.index')
                ->with('success', 'Receitas consolidadas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao consolidar receitas: ' . $e->getMessage());
        }
    }

    /**
     * Listar receitas
     */
    public function index(Request $request)
    {
        $query = Receita::query();

        if ($request->filled('data_inicio')) {
            $query->whereDate('data', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data', '<=', $request->data_fim);
        }

        if ($request->filled('origem')) {
            $query->where('origem', $request->origem);
        }

        $receitas = $query->orderBy('data', 'desc')->paginate(20);

        $totalReceitas = $query->sum('valor_total');

        return view('financeiro.receitas.index', compact('receitas', 'totalReceitas'));
    }
}
