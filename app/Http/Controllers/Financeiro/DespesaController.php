<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Despesa;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class DespesaController extends Controller
{

    /**
     * Listar despesas
     */
    public function index(Request $request)
    {
        $query = Despesa::with(['veiculo', 'aprovador']);

        if ($request->filled('veiculo_id')) {
            $query->where('veiculo_id', $request->veiculo_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data', '<=', $request->data_fim);
        }

        if ($request->filled('aprovado')) {
            $query->where('aprovado', $request->boolean('aprovado'));
        }

        $despesas = $query->orderBy('data', 'desc')->paginate(20);
        $veiculos = Veiculo::select('id', 'placa', 'marca', 'modelo')->get();

        $totalDespesas = $query->sum('valor');

        return view('financeiro.despesas.index', compact('despesas', 'veiculos', 'totalDespesas'));
    }

    /**
     * Registrar despesa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'nullable|exists:veiculo,id',
            'tipo' => 'required|in:MANUTENCAO,COMBUSTIVEL,SEGURO,MULTA,SALARIO,OUTRO',
            'valor' => 'required|numeric|min:0',
            'data' => 'required|date',
            'descricao' => 'nullable|string|max:255',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        DB::beginTransaction();
        try {
            $dados = [
                'veiculo_id' => $validated['veiculo_id'],
                'tipo' => $validated['tipo'],
                'valor' => $validated['valor'],
                'data' => $validated['data'],
                'descricao' => $validated['descricao'],
                'aprovado' => false
            ];

            if ($request->hasFile('documento')) {
                $path = $request->file('documento')->store('despesas', 'public');
                $dados['documento_url'] = Storage::url($path);
            }

            $despesa = Despesa::create($dados);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'despesa',
                $despesa->id,
                null,
                $despesa->toArray()
            );

            DB::commit();

            return redirect()->route('despesas.index')
                ->with('success', 'Despesa registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar despesa: ' . $e->getMessage());
        }
    }

    /**
     * Aprovar despesa
     */
    public function aprovar(Despesa $despesa)
    {
        DB::beginTransaction();
        try {
            $dadosAntigos = $despesa->toArray();
            $despesa->aprovar(Auth::id());

            LogAuditoria::registrar(
                Auth::id(),
                'APROVAR_DESPESA',
                'despesa',
                $despesa->id,
                $dadosAntigos,
                $despesa->toArray()
            );

            DB::commit();

            return redirect()->route('despesas.index')
                ->with('success', 'Despesa aprovada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao aprovar despesa: ' . $e->getMessage());
        }
    }
}
