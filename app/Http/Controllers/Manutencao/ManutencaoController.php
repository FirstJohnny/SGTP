<?php

namespace App\Http\Controllers\Manutencao;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Manutencao;
use App\Models\PecaTrocada;
use App\Models\LogAuditoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class ManutencaoController extends Controller
{

    /**
     * Listar manutenções
     */
    public function index(Request $request)
    {
        $query = Manutencao::with(['veiculo', 'ocorrencia', 'pecasTrocadas']);

        if ($request->filled('veiculo_id')) {
            $query->where('veiculo_id', $request->veiculo_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $manutencoes = $query->orderBy('created_at', 'desc')->paginate(20);
        $veiculos = Veiculo::select('id', 'placa', 'marca', 'modelo')->get();

        return view('manutencoes.index', compact('manutencoes', 'veiculos'));
    }

    /**
     * Agendar manutenção
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,id',
            'ocorrencia_id' => 'nullable|exists:ocorrencia,id',
            'tipo' => 'required|in:PREVENTIVA,CORRETIVA,EMERGENCIAL',
            'descricao' => 'required|string',
            'data_agendamento' => 'required|date|after_or_equal:today',
            'oficina' => 'required|string|max:150',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $veiculo = Veiculo::findOrFail($validated['veiculo_id']);

            $manutencao = Manutencao::create([
                ...$validated,
                'custo_pecas' => 0,
                'custo_mao_obra' => 0,
                'custo_total' => 0,
                'status' => Manutencao::STATUS_AGENDADA
            ]);

            // Atualizar status do veículo
            $veiculo->update(['status' => Veiculo::STATUS_MANUTENCAO]);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'manutencao',
                $manutencao->id,
                null,
                $manutencao->toArray()
            );

            DB::commit();

            return redirect()->route('manutencoes.index')
                ->with('success', 'Manutenção agendada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao agendar manutenção: ' . $e->getMessage());
        }
    }

    /**
     * Registrar execução de manutenção
     */
    public function executar(Request $request, Manutencao $manutencao)
    {
        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'custo_pecas' => 'nullable|numeric|min:0',
            'custo_mao_obra' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
            'pecas' => 'array',
            'pecas.*.nome_peca' => 'required|string|max:150',
            'pecas.*.quantidade' => 'required|integer|min:1',
            'pecas.*.preco_unitario' => 'required|numeric|min:0',
            'pecas.*.garantia_meses' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $manutencao->toArray();

            $manutencao->update([
                'data_inicio' => $validated['data_inicio'],
                'data_fim' => $validated['data_fim'],
                'custo_pecas' => $validated['custo_pecas'] ?? 0,
                'custo_mao_obra' => $validated['custo_mao_obra'] ?? 0,
                'observacoes' => $validated['observacoes'] ?? $manutencao->observacoes,
                'status' => Manutencao::STATUS_CONCLUIDA
            ]);

            $manutencao->calcularCustoTotal();

            // Registrar peças trocadas
            if (isset($validated['pecas'])) {
                foreach ($validated['pecas'] as $peca) {
                    PecaTrocada::create([
                        'manutencao_id' => $manutencao->id,
                        'nome_peca' => $peca['nome_peca'],
                        'quantidade' => $peca['quantidade'],
                        'preco_unitario' => $peca['preco_unitario'],
                        'garantia_meses' => $peca['garantia_meses'] ?? null
                    ]);
                }
            }

            // Atualizar veículo
            $veiculo = $manutencao->veiculo;
            $veiculo->ultima_inspecao = now();
            $veiculo->proxima_inspecao = $this->calcularProximaInspecao($veiculo);
            $veiculo->status = Veiculo::STATUS_ATIVO;
            $veiculo->save();

            LogAuditoria::registrar(
                Auth::id(),
                'EXECUTAR_MANUTENCAO',
                'manutencao',
                $manutencao->id,
                $dadosAntigos,
                $manutencao->toArray()
            );

            DB::commit();

            return redirect()->route('manutencoes.show', $manutencao)
                ->with('success', 'Manutenção registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar manutenção: ' . $e->getMessage());
        }
    }

    /**
     * Calcular próxima inspeção baseada em km ou tempo (RN09)
     */
    private function calcularProximaInspecao(Veiculo $veiculo): Carbon
    {
        $kmIntervalo = 10000; // 10.000 km
        $mesesIntervalo = 6;   // 6 meses

        $proximaPorKm = clone $veiculo->ultima_inspecao;
        $proximaPorKm->addDays($kmIntervalo / ($veiculo->consumo_medio ?? 50));

        $proximaPorTempo = clone $veiculo->ultima_inspecao;
        $proximaPorTempo->addMonths($mesesIntervalo);

        return $proximaPorKm->lt($proximaPorTempo) ? $proximaPorKm : $proximaPorTempo;
    }
}
