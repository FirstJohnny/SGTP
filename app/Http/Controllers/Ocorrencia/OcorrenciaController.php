<?php

namespace App\Http\Controllers\Ocorrencia;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Escala;
use App\Models\Colaborador;
use App\Models\Ocorrencia;
use App\Models\Manutencao;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class OcorrenciaController extends Controller
{

    /**
     * Listar ocorrências
     */
    public function index(Request $request)
    {
        $query = Ocorrencia::with(['veiculo', 'colaborador', 'escala']);

        if ($request->filled('veiculo_id')) {
            $query->where('veiculo_id', $request->veiculo_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_ocorrencia', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_ocorrencia', '<=', $request->data_fim);
        }

        $ocorrencias = $query->orderBy('data_ocorrencia', 'desc')->paginate(20);

        $veiculos = Veiculo::select('id', 'placa', 'marca', 'modelo')->get();

        return view('ocorrencias.index', compact('ocorrencias', 'veiculos'));
    }

    /**
     * Show form to create a new occurrence
     */
    public function create()
    {
        $veiculos = Veiculo::where('status', 'ATIVO')->get();
        $escalas = Escala::where('status', 'EM_ANDAMENTO')->get();
        
        return view('ocorrencias.create', compact('veiculos', 'escalas'));
    }

    /**
     * Registrar ocorrência
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,id',
            'escala_id' => 'nullable|exists:escala,id',
            'colaborador_id' => 'nullable|exists:colaborador,id',
            'tipo' => 'required|in:ACIDENTE,ATRASO,FALHA_MECANICA,ASSALTO,OUTRO',
            'gravidade' => 'required|in:LEVE,MEDIA,GRAVE,CRITICA',
            'descricao' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'data_ocorrencia' => 'required|date',
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|max:5120',
            'status' => 'required|in:ABERTA,EM_ANALISE,RESOLVIDA,CANCELADA'
        ]);

        DB::beginTransaction();
        try {
            $dados = [
                'veiculo_id' => $validated['veiculo_id'],
                'escala_id' => $validated['escala_id'],
                'colaborador_id' => $validated['colaborador_id'] ?? Auth::user()->colaborador?->id,
                'tipo' => $validated['tipo'],
                'gravidade' => $validated['gravidade'],
                'descricao' => $validated['descricao'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'data_ocorrencia' => $validated['data_ocorrencia'],
                'status' => $validated['status'],
                'sincronizado' => false
            ];

            // Upload de fotos
            if ($request->hasFile('fotos')) {
                $fotosUrls = [];
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('ocorrencias', 'public');
                    $fotosUrls[] = Storage::url($path);
                }
                $dados['fotos_url'] = json_encode($fotosUrls);
            }

            $ocorrencia = Ocorrencia::create($dados);

            // Se for falha mecânica grave, criar manutenção automaticamente (RN08)
            if ($validated['tipo'] === 'FALHA_MECANICA' && in_array($validated['gravidade'], ['GRAVE', 'CRITICA'])) {
                $this->criarManutencaoAutomatica($ocorrencia);
            }

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'ocorrencia',
                $ocorrencia->id,
                null,
                $ocorrencia->toArray()
            );

            DB::commit();

            return redirect()->route('ocorrencias.index')
                ->with('success', 'Ocorrência registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar ocorrência: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified occurrence
     */
    public function show(Ocorrencia $ocorrencia)
    {
        return view('ocorrencias.show', compact('ocorrencia'));
    }

    /**
     * Atualizar ocorrência
     */
    public function update(Request $request, Ocorrencia $ocorrencia)
    {
        $validated = $request->validate([
            'status' => 'required|in:ABERTA,EM_ANALISE,RESOLVIDA,CANCELADA',
            'descricao' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $ocorrencia->toArray();
            $ocorrencia->update($validated);

            if ($validated['status'] === Ocorrencia::STATUS_RESOLVIDA) {
                $ocorrencia->resolver();
            }

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'ocorrencia',
                $ocorrencia->id,
                $dadosAntigos,
                $ocorrencia->toArray()
            );

            DB::commit();

            return redirect()->route('ocorrencias.index')
                ->with('success', 'Ocorrência atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar ocorrência: ' . $e->getMessage());
        }
    }

    /**
     * Últimas ocorrências (para dashboard)
     */
    public function ultimas()
    {
        $ocorrencias = Ocorrencia::with(['veiculo'])
            ->orderBy('data_ocorrencia', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($oc) {
                return [
                    'veiculo_placa' => $oc->veiculo->placa ?? 'N/A',
                    'tipo' => $oc->tipo,
                    'gravidade' => $oc->gravidade,
                    'data_ocorrencia' => $oc->data_ocorrencia->format('d/m/Y H:i'),
                    'status' => $oc->status,
                    'status_class' => $this->getStatusClass($oc->status)
                ];
            });

        return response()->json($ocorrencias);
    }

    private function criarManutencaoAutomatica(Ocorrencia $ocorrencia)
    {
        Manutencao::create([
            'veiculo_id' => $ocorrencia->veiculo_id,
            'ocorrencia_id' => $ocorrencia->id,
            'tipo' => Manutencao::TIPO_CORRETIVA,
            'descricao' => 'Manutenção automática gerada a partir da ocorrência: ' . $ocorrencia->descricao,
            'data_agendamento' => now()->addDay(),
            'oficina' => 'Oficina Credenciada',
            'custo_total' => 0,
            'status' => Manutencao::STATUS_AGENDADA
        ]);

        // Atualizar status do veículo
        $ocorrencia->veiculo->update(['status' => Veiculo::STATUS_MANUTENCAO]);
    }

    private function getStatusClass($status)
    {
        return match($status) {
            Ocorrencia::STATUS_ABERTA => 'pendente',
            Ocorrencia::STATUS_EM_ANALISE => 'em-andamento',
            Ocorrencia::STATUS_RESOLVIDA => 'ativo',
            default => 'pendente'
        };
    }
}