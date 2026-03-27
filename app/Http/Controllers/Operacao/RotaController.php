<?php

namespace App\Http\Controllers\Operacao;

use App\Http\Controllers\Controller;
use App\Models\Rota;
use App\Models\PontoParagem;
use App\Models\Horario;
use App\Models\Tarifa;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class RotaController extends Controller
{

    /**
     * Listar rotas
     */
    public function index(Request $request)
    {
        $query = Rota::query();

        if ($request->filled('search')) {
            $query->where('nome', 'like', "%{$request->search}%")
                  ->orWhere('codigo', 'like', "%{$request->search}%");
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('ativa')) {
            $query->where('ativa', $request->boolean('ativa'));
        }

        $rotas = $query->orderBy('nome')->paginate(15);

        return view('operacao.rotas.index', compact('rotas'));
    }

    /**
     * Formulário de cadastro
     */
    public function create()
    {
        $pontos = PontoParagem::orderBy('nome')->get();
        return view('operacao.rotas.create', compact('pontos'));
    }

    /**
     * Cadastrar rota
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'codigo' => 'required|string|max:50|unique:rota',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:URBANA,INTERMUNICIPAL,RODOVIARIA,ESCOLAR',
            'distancia_total' => 'required|numeric|min:0',
            'tempo_estimado' => 'nullable|integer|min:1',
            'trajeto_geojson' => 'nullable|json',
            'ativa' => 'boolean',
            'empresa_responsavel' => 'nullable|string|max:150',
            'pontos' => 'array',
            'pontos.*.id' => 'exists:ponto_paragem,id',
            'pontos.*.ordem' => 'required|integer|min:1',
            'pontos.*.tempo_estimado' => 'nullable|integer',
            'pontos.*.distancia' => 'nullable|numeric'
        ]);

        DB::beginTransaction();
        try {
            $validated['ativa'] = $request->boolean('ativa');
            $rota = Rota::create($validated);

            // Associar pontos de paragem
            if ($request->has('pontos')) {
                foreach ($request->pontos as $ponto) {
                    $rota->pontosRota()->create([
                        'ponto_paragem_id' => $ponto['id'],
                        'ordem' => $ponto['ordem'],
                        'tempo_estimado_chegada' => $ponto['tempo_estimado'] ?? null,
                        'distancia_desde_inicio' => $ponto['distancia'] ?? null
                    ]);
                }
            }

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'rota',
                $rota->id,
                null,
                $rota->toArray()
            );

            DB::commit();

            return redirect()->route('rotas.index')
                ->with('success', 'Rota cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar rota: ' . $e->getMessage());
        }
    }

    /**
     * Exibir detalhes da rota
     */
    public function show(Rota $rota)
    {
        $pontos = $rota->pontosParagem()->orderBy('pivot_ordem')->get();
        $horarios = $rota->horarios()->orderBy('hora_partida')->get();
        $tarifas = $rota->tarifas()->where('ativa', true)->get();

        return view('operacao.rotas.show', compact('rota', 'pontos', 'horarios', 'tarifas'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Rota $rota)
    {
        $pontos = PontoParagem::orderBy('nome')->get();
        $pontosSelecionados = $rota->pontosRota()->orderBy('ordem')->get();

        return view('operacao.rotas.edit', compact('rota', 'pontos', 'pontosSelecionados'));
    }

    /**
     * Atualizar rota
     */
    public function update(Request $request, Rota $rota)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'codigo' => 'required|string|max:50|unique:rota,codigo,' . $rota->id,
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:URBANA,INTERMUNICIPAL,RODOVIARIA,ESCOLAR',
            'distancia_total' => 'required|numeric|min:0',
            'tempo_estimado' => 'nullable|integer|min:1',
            'trajeto_geojson' => 'nullable|json',
            'ativa' => 'boolean',
            'empresa_responsavel' => 'nullable|string|max:150',
            'pontos' => 'array',
            'pontos.*.id' => 'exists:ponto_paragem,id',
            'pontos.*.ordem' => 'required|integer|min:1',
            'pontos.*.tempo_estimado' => 'nullable|integer',
            'pontos.*.distancia' => 'nullable|numeric'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $rota->toArray();
            $validated['ativa'] = $request->boolean('ativa');
            $rota->update($validated);

            // Atualizar pontos
            $rota->pontosRota()->delete();

            if ($request->has('pontos')) {
                foreach ($request->pontos as $ponto) {
                    $rota->pontosRota()->create([
                        'ponto_paragem_id' => $ponto['id'],
                        'ordem' => $ponto['ordem'],
                        'tempo_estimado_chegada' => $ponto['tempo_estimado'] ?? null,
                        'distancia_desde_inicio' => $ponto['distancia'] ?? null
                    ]);
                }
            }

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'rota',
                $rota->id,
                $dadosAntigos,
                $rota->toArray()
            );

            DB::commit();

            return redirect()->route('rotas.index')
                ->with('success', 'Rota atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar rota: ' . $e->getMessage());
        }
    }

    /**
     * Remover rota (soft delete)
     */
    public function destroy(Rota $rota)
    {
        DB::beginTransaction();
        try {
            $dados = $rota->toArray();
            $rota->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'rota',
                $rota->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('rotas.index')
                ->with('success', 'Rota removida com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover rota: ' . $e->getMessage());
        }
    }

    /**
     * API pública: Listar rotas disponíveis
     */
    public function publicRoutes()
    {
        $rotas = Rota::where('ativa', true)
            ->select('id', 'nome', 'codigo', 'tipo', 'distancia_total')
            ->get();

        return response()->json($rotas);
    }
}
