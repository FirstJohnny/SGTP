<?php

namespace App\Http\Controllers\Frota;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Abastecimento;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class AbastecimentoController extends Controller
{

    /**
     * Listar abastecimentos
     */
    public function index(Request $request)
    {
        $query = Abastecimento::with(['veiculo', 'motorista']);

        if ($request->filled('veiculo_id')) {
            $query->where('veiculo_id', $request->veiculo_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data', '<=', $request->data_fim);
        }

        $abastecimentos = $query->orderBy('data', 'desc')->paginate(20);
        $veiculos = Veiculo::select('id', 'placa', 'marca', 'modelo')->get();

        return view('frota.abastecimentos.index', compact('abastecimentos', 'veiculos'));
    }

    /**
     * Formulário para registrar abastecimento
     */
    public function create()
    {
        $veiculos = Veiculo::where('status', Veiculo::STATUS_ATIVO)
            ->select('id', 'placa', 'marca', 'modelo')
            ->get();

        return view('frota.abastecimentos.create', compact('veiculos'));
    }

    /**
     * Registrar abastecimento
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,id',
            'motorista_id' => 'nullable|exists:colaborador,id',
            'data' => 'required|date',
            'odometro' => 'required|integer|min:0',
            'litros' => 'required|numeric|min:0.01',
            'valor_total' => 'required|numeric|min:0.01',
            'preco_litro' => 'required|numeric|min:0.01',
            'posto' => 'required|string|max:150',
            'tipo_combustivel' => 'required|in:DIESEL,GASOLINA,ELETRICO,HIBRIDO',
            'comprovativo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        DB::beginTransaction();
        try {
            // Verificar se odômetro é maior que o último registro
            $ultimoAbastecimento = Abastecimento::where('veiculo_id', $validated['veiculo_id'])
                ->orderBy('data', 'desc')
                ->first();

            if ($ultimoAbastecimento && $validated['odometro'] < $ultimoAbastecimento->odometro) {
                return back()->with('error', 'Odômetro não pode ser menor que o último registro ('
                    . $ultimoAbastecimento->odometro . ' km)');
            }

            $dados = [
                'veiculo_id' => $validated['veiculo_id'],
                'motorista_id' => $validated['motorista_id'],
                'data' => $validated['data'],
                'odometro' => $validated['odometro'],
                'litros' => $validated['litros'],
                'valor_total' => $validated['valor_total'],
                'preco_litro' => $validated['preco_litro'],
                'posto' => $validated['posto'],
                'tipo_combustivel' => $validated['tipo_combustivel']
            ];

            if ($request->hasFile('comprovativo')) {
                $path = $request->file('comprovativo')->store('abastecimentos', 'public');
                $dados['comprovativo_url'] = Storage::url($path);
            }

            $abastecimento = Abastecimento::create($dados);

            // Atualizar km atual do veículo
            $veiculo = Veiculo::find($validated['veiculo_id']);
            if ($validated['odometro'] > $veiculo->km_atual) {
                $veiculo->km_atual = $validated['odometro'];
                $veiculo->save();
            }

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'abastecimento',
                $abastecimento->id,
                null,
                $dados
            );

            DB::commit();

            return redirect()->route('abastecimentos.index')
                ->with('success', 'Abastecimento registrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar abastecimento: ' . $e->getMessage());
        }
    }

    /**
     * Exibir detalhes do abastecimento
     */
    public function show(Abastecimento $abastecimento)
    {
        return view('frota.abastecimentos.show', compact('abastecimento'));
    }

    /**
     * Remover abastecimento
     */
    public function destroy(Abastecimento $abastecimento)
    {
        DB::beginTransaction();
        try {
            $dados = $abastecimento->toArray();

            // Remover comprovativo se existir
            if ($abastecimento->comprovativo_url) {
                $path = str_replace('/storage/', '', $abastecimento->comprovativo_url);
                Storage::disk('public')->delete($path);
            }

            $abastecimento->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'abastecimento',
                $abastecimento->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('abastecimentos.index')
                ->with('success', 'Abastecimento removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover abastecimento: ' . $e->getMessage());
        }
    }

    /**
     * Relatório de consumo por veículo
     */
    public function relatorio(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio'
        ]);

        $abastecimentos = Abastecimento::where('veiculo_id', $validated['veiculo_id'])
            ->whereBetween('data', [$validated['data_inicio'], $validated['data_fim']])
            ->orderBy('data')
            ->get();

        $totalLitros = $abastecimentos->sum('litros');
        $totalValor = $abastecimentos->sum('valor_total');
        $mediaPreco = $totalLitros > 0 ? $totalValor / $totalLitros : 0;

        // Calcular consumo médio
        $consumo = [];
        $anterior = null;
        foreach ($abastecimentos as $abastecimento) {
            if ($anterior) {
                $kmRodados = $abastecimento->odometro - $anterior->odometro;
                if ($kmRodados > 0 && $anterior->litros > 0) {
                    $consumo[] = $kmRodados / $anterior->litros;
                }
            }
            $anterior = $abastecimento;
        }

        $consumoMedio = !empty($consumo) ? array_sum($consumo) / count($consumo) : 0;

        return response()->json([
            'total_litros' => round($totalLitros, 2),
            'total_valor' => number_format($totalValor, 2),
            'media_preco' => number_format($mediaPreco, 2),
            'consumo_medio' => round($consumoMedio, 2),
            'abastecimentos' => $abastecimentos
        ]);
    }
}
