<?php

namespace App\Http\Controllers\Bilhetica;

use App\Http\Controllers\Controller;
use App\Models\Rota;
use App\Models\Tarifa;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class TarifaController extends Controller
{

    /**
     * Listar tarifas
     */
    public function index(Request $request)
    {
        $query = Tarifa::with('rota');

        if ($request->filled('rota_id')) {
            $query->where('rota_id', $request->rota_id);
        }

        if ($request->filled('ativa')) {
            $query->where('ativa', $request->boolean('ativa'));
        }

        $tarifas = $query->orderBy('created_at', 'desc')->paginate(15);
        $rotas = Rota::where('ativa', true)->get();

        return view('bilhetica.tarifas.index', compact('tarifas', 'rotas'));
    }

    /**
     * Criar tarifa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rota_id' => 'required|exists:rota,id',
            'tipo_passageiro' => 'required|in:ADULTO,ESTUDANTE,IDOSO,OUTRO',
            'valor' => 'required|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after:data_inicio',
            'ativa' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Desativar tarifas anteriores do mesmo tipo
            Tarifa::where('rota_id', $validated['rota_id'])
                ->where('tipo_passageiro', $validated['tipo_passageiro'])
                ->where('ativa', true)
                ->update(['ativa' => false]);

            $validated['ativa'] = $request->boolean('ativa');
            $tarifa = Tarifa::create($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'tarifa',
                $tarifa->id,
                null,
                $tarifa->toArray()
            );

            DB::commit();

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar tarifa: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar tarifa
     */
    public function update(Request $request, Tarifa $tarifa)
    {
        $validated = $request->validate([
            'valor' => 'required|numeric|min:0',
            'data_fim' => 'nullable|date|after:data_inicio',
            'ativa' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $tarifa->toArray();
            $validated['ativa'] = $request->boolean('ativa');
            $tarifa->update($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'tarifa',
                $tarifa->id,
                $dadosAntigos,
                $tarifa->toArray()
            );

            DB::commit();

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar tarifa: ' . $e->getMessage());
        }
    }

    /**
     * Remover tarifa
     */
    public function destroy(Tarifa $tarifa)
    {
        DB::beginTransaction();
        try {
            $dados = $tarifa->toArray();
            $tarifa->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'tarifa',
                $tarifa->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('tarifas.index')
                ->with('success', 'Tarifa removida com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover tarifa: ' . $e->getMessage());
        }
    }

    /**
     * API: Tarifas públicas
     */
    public function publicTarifas(Request $request)
    {
        $validated = $request->validate([
            'rota_id' => 'required|exists:rota,id',
            'tipo_passageiro' => 'nullable|in:ADULTO,ESTUDANTE,IDOSO,OUTRO'
        ]);

        $query = Tarifa::where('rota_id', $validated['rota_id'])
            ->where('ativa', true)
            ->where('data_inicio', '<=', now())
            ->where(function($q) {
                $q->whereNull('data_fim')->orWhere('data_fim', '>=', now());
            });

        if (isset($validated['tipo_passageiro'])) {
            $query->where('tipo_passageiro', $validated['tipo_passageiro']);
        }

        return response()->json($query->get());
    }
}
