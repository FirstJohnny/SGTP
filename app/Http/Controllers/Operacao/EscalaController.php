<?php

namespace App\Http\Controllers\Operacao;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Colaborador;
use App\Models\Rota;
use App\Models\Escala;
use App\Models\LogAuditoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class EscalaController extends Controller
{

    /**
     * Listar escalas
     */
    public function index(Request $request)
    {
        $query = Escala::with(['veiculo', 'motorista', 'cobrador', 'rota']);

        if ($request->filled('data')) {
            $query->whereDate('data', $request->data);
        } else {
            $query->whereDate('data', Carbon::today());
        }

        if ($request->filled('rota_id')) {
            $query->where('rota_id', $request->rota_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $escalas = $query->orderBy('hora_inicio')->get();

        $veiculos = Veiculo::where('status', Veiculo::STATUS_ATIVO)->get();
        $motoristas = Colaborador::where('tipo', Colaborador::TIPO_MOTORISTA)
            ->whereNull('data_demissao')
            ->get();
        $cobradores = Colaborador::where('tipo', Colaborador::TIPO_COBRADOR)
            ->whereNull('data_demissao')
            ->get();
        $rotas = Rota::where('ativa', true)->get();

        return view('operacao.escalas.index', compact('escalas', 'veiculos', 'motoristas', 'cobradores', 'rotas'));
    }

    /**
     * Criar escala
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,id',
            'motorista_id' => 'required|exists:colaborador,id',
            'cobrador_id' => 'nullable|exists:colaborador,id',
            'rota_id' => 'required|exists:rota,id',
            'data' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Validar regras de negócio
            $veiculo = Veiculo::find($validated['veiculo_id']);
            if (!$veiculo->isDisponivel()) {
                return back()->with('error', 'Veículo não está disponível para operação');
            }

            $motorista = Colaborador::find($validated['motorista_id']);
            if (!$motorista->cartaValida()) {
                return back()->with('error', 'Carta do motorista expirada');
            }

            // Verificar limite de horas de condução (RN14)
            $horasConducaoHoje = Escala::where('motorista_id', $validated['motorista_id'])
                ->whereDate('data', $validated['data'])
                ->sum(DB::raw("TIME_TO_SEC(TIMEDIFF(hora_fim, hora_inicio)) / 3600"));

            $novaDuracao = (strtotime($validated['hora_fim']) - strtotime($validated['hora_inicio'])) / 3600;

            if ($horasConducaoHoje + $novaDuracao > 8) {
                return back()->with('error', 'Motorista excederia limite de 8 horas de condução por dia');
            }

            // Verificar conflitos de horário
            $conflito = Escala::where('veiculo_id', $validated['veiculo_id'])
                ->whereDate('data', $validated['data'])
                ->where(function($q) use ($validated) {
                    $q->whereBetween('hora_inicio', [$validated['hora_inicio'], $validated['hora_fim']])
                      ->orWhereBetween('hora_fim', [$validated['hora_inicio'], $validated['hora_fim']])
                      ->orWhere(function($q2) use ($validated) {
                          $q2->where('hora_inicio', '<=', $validated['hora_inicio'])
                             ->where('hora_fim', '>=', $validated['hora_fim']);
                      });
                })
                ->exists();

            if ($conflito) {
                return back()->with('error', 'Veículo já possui escala neste horário');
            }

            $escala = Escala::create([
                ...$validated,
                'status' => Escala::STATUS_PENDENTE
            ]);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'escala',
                $escala->id,
                null,
                $escala->toArray()
            );

            DB::commit();

            return redirect()->route('escalas.index')
                ->with('success', 'Escala criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar escala: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar escala
     */
    public function update(Request $request, Escala $escala)
    {
        // Verificar cancelamento com antecedência (RN04)
        if ($request->status === Escala::STATUS_CANCELADA) {
            $horaInicio = Carbon::parse($escala->data . ' ' . $escala->hora_inicio);
            if (now()->diffInHours($horaInicio, false) < 2 && $horaInicio->isFuture()) {
                return back()->with('error', 'Cancelamento só permitido com 2 horas de antecedência');
            }
        }

        $validated = $request->validate([
            'status' => 'required|in:PENDENTE,EM_ANDAMENTO,FINALIZADA,CANCELADA',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $escala->toArray();
            $escala->update($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'escala',
                $escala->id,
                $dadosAntigos,
                $escala->toArray()
            );

            DB::commit();

            return redirect()->route('escalas.index')
                ->with('success', 'Escala atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar escala: ' . $e->getMessage());
        }
    }

    /**
     * Remover escala
     */
    public function destroy(Escala $escala)
    {
        DB::beginTransaction();
        try {
            $dados = $escala->toArray();
            $escala->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'escala',
                $escala->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('escalas.index')
                ->with('success', 'Escala removida com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover escala: ' . $e->getMessage());
        }
    }

    /**
     * API: Escalas do dia
     */
    public function diarias(Request $request)
    {
        $data = $request->filled('data') ? Carbon::parse($request->data) : Carbon::today();

        $escalas = Escala::with(['veiculo', 'motorista', 'cobrador', 'rota'])
            ->whereDate('data', $data)
            ->orderBy('hora_inicio')
            ->get();

        return response()->json($escalas);
    }
}
