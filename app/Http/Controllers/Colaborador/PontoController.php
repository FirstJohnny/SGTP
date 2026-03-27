<?php

namespace App\Http\Controllers\Colaborador;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use App\Models\Escala;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class PontoController extends Controller
{

    /**
     * Registrar início de serviço (para motoristas)
     */
    public function iniciarServico(Request $request)
    {
        $validated = $request->validate([
            'escala_id' => 'required|exists:escala,id',
            'km_inicial' => 'required|integer|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        DB::beginTransaction();
        try {
            $escala = Escala::findOrFail($validated['escala_id']);

            // Verificar se o motorista é o da escala
            if ($escala->motorista_id != Auth::user()->colaborador?->id) {
                return response()->json(['error' => 'Você não é o motorista desta escala'], 403);
            }

            // Verificar se já foi iniciada
            if ($escala->status !== Escala::STATUS_PENDENTE) {
                return response()->json(['error' => 'Escala já foi iniciada ou finalizada'], 400);
            }

            // Verificar se o veículo está disponível
            $veiculo = $escala->veiculo;
            if (!$veiculo->isDisponivel()) {
                return response()->json(['error' => 'Veículo não está disponível para operação'], 400);
            }

            // Verificar documentação do motorista
            if (!$escala->motorista->cartaValida()) {
                return response()->json(['error' => 'Carta de condução expirada'], 400);
            }

            $escala->iniciar($validated['km_inicial']);

            // Registrar ponto de início
            if ($request->filled('latitude')) {
                $escala->rastreamentos()->create([
                    'veiculo_id' => $escala->veiculo_id,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'timestamp' => now(),
                    'ignicao' => true
                ]);
            }

            LogAuditoria::registrar(
                Auth::id(),
                'INICIAR_SERVICO',
                'escala',
                $escala->id,
                null,
                ['km_inicial' => $validated['km_inicial']]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Serviço iniciado com sucesso!',
                'escala' => $escala
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Registrar fim de serviço
     */
    public function finalizarServico(Request $request)
    {
        $validated = $request->validate([
            'escala_id' => 'required|exists:escala,id',
            'km_final' => 'required|integer|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        DB::beginTransaction();
        try {
            $escala = Escala::findOrFail($validated['escala_id']);

            // Verificar se o motorista é o da escala
            if ($escala->motorista_id != Auth::user()->colaborador?->id) {
                return response()->json(['error' => 'Você não é o motorista desta escala'], 403);
            }

            // Verificar se está em andamento
            if ($escala->status !== Escala::STATUS_EM_ANDAMENTO) {
                return response()->json(['error' => 'Escala não está em andamento'], 400);
            }

            // Validar km final
            if ($validated['km_final'] < $escala->km_inicial) {
                return response()->json(['error' => 'KM final não pode ser menor que KM inicial'], 400);
            }

            $escala->finalizar($validated['km_final']);

            // Registrar ponto de fim
            if ($request->filled('latitude')) {
                $escala->rastreamentos()->create([
                    'veiculo_id' => $escala->veiculo_id,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'timestamp' => now(),
                    'ignicao' => false
                ]);
            }

            LogAuditoria::registrar(
                Auth::id(),
                'FINALIZAR_SERVICO',
                'escala',
                $escala->id,
                null,
                ['km_final' => $validated['km_final']]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Serviço finalizado com sucesso!',
                'km_percorrido' => $escala->kmPercorrido()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Escalas do motorista logado
     */
    public function minhasEscalas(Request $request)
    {
        $colaborador = Auth::user()->colaborador;

        if (!$colaborador) {
            return response()->json(['error' => 'Perfil não configurado'], 404);
        }

        $query = Escala::where('motorista_id', $colaborador->id)
            ->with(['veiculo', 'rota']);

        if ($request->filled('data')) {
            $query->whereDate('data', $request->data);
        }

        $escalas = $query->orderBy('data', 'desc')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json($escalas);
    }
}
