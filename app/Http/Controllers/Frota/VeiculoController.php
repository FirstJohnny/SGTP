<?php

namespace App\Http\Controllers\Frota;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\DocumentoVeiculo;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class VeiculoController extends Controller
{

    /**
     * Listar veículos
     */
    public function index(Request $request)
    {
        $query = Veiculo::query();

        if ($request->filled('search')) {
            $query->where('placa', 'like', "%{$request->search}%")
                  ->orWhere('marca', 'like', "%{$request->search}%")
                  ->orWhere('modelo', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $veiculos = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'data' => $veiculos->items(),
                'current_page' => $veiculos->currentPage(),
                'last_page' => $veiculos->lastPage()
            ]);
        }

        return view('frota.index', compact('veiculos'));
    }

    /**
     * Formulário de cadastro
     */
    public function create()
    {
        return view('frota.create');
    }

    /**
     * Cadastrar novo veículo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:veiculo',
            'chassi' => 'required|string|max:50|unique:veiculo',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'ano_fabricado' => 'required|integer|min:1990|max:' . date('Y'),
            'cor' => 'required|string|max:50',
            'lotacao' => 'required|integer|min:1',
            'tipo_combustivel' => 'required|in:DIESEL,GASOLINA,ELETRICO,HIBRIDO',
            'consumo_medio' => 'nullable|numeric|min:0',
            'km_atual' => 'required|integer|min:0',
            'data_aquisicao' => 'required|date',
            'status' => 'required|in:ATIVO,MANUTENCAO,INATIVO',
            'seguro_validade' => 'required|date|after:today',
            'ultima_inspecao' => 'nullable|date',
            'proxima_inspecao' => 'nullable|date|after:ultima_inspecao',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $veiculo = Veiculo::create($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'veiculo',
                $veiculo->id,
                null,
                $veiculo->toArray()
            );

            DB::commit();

            return redirect()->route('frota.index')
                ->with('success', 'Veículo cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar veículo: ' . $e->getMessage());
        }
    }

    /**
     * Exibir detalhes do veículo
     */
    public function show(Veiculo $veiculo)
    {
        $documentos = $veiculo->documentos()->latest()->get();
        $manutencoes = $veiculo->manutencoes()->latest()->limit(10)->get();
        $abastecimentos = $veiculo->abastecimentos()->latest()->limit(10)->get();

        return view('frota.show', compact('veiculo', 'documentos', 'manutencoes', 'abastecimentos'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Veiculo $veiculo)
    {
        return view('frota.edit', compact('veiculo'));
    }

    /**
     * Atualizar veículo
     */
    public function update(Request $request, Veiculo $veiculo)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:veiculo,placa,' . $veiculo->id,
            'chassi' => 'required|string|max:50|unique:veiculo,chassi,' . $veiculo->id,
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'ano_fabricado' => 'required|integer|min:1990|max:' . date('Y'),
            'cor' => 'required|string|max:50',
            'lotacao' => 'required|integer|min:1',
            'tipo_combustivel' => 'required|in:DIESEL,GASOLINA,ELETRICO,HIBRIDO',
            'consumo_medio' => 'nullable|numeric|min:0',
            'km_atual' => 'required|integer|min:0',
            'data_aquisicao' => 'required|date',
            'status' => 'required|in:ATIVO,MANUTENCAO,INATIVO',
            'seguro_validade' => 'required|date',
            'ultima_inspecao' => 'nullable|date',
            'proxima_inspecao' => 'nullable|date',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $veiculo->toArray();
            $veiculo->update($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'veiculo',
                $veiculo->id,
                $dadosAntigos,
                $veiculo->toArray()
            );

            DB::commit();

            return redirect()->route('frota.index')
                ->with('success', 'Veículo atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar veículo: ' . $e->getMessage());
        }
    }

    /**
     * Remover veículo (soft delete)
     */
    public function destroy(Veiculo $veiculo)
    {
        DB::beginTransaction();
        try {
            $dados = $veiculo->toArray();
            $veiculo->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'veiculo',
                $veiculo->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('frota.index')
                ->with('success', 'Veículo removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover veículo: ' . $e->getMessage());
        }
    }

    /**
     * API: Listar veículos para selects
     */
    public function list(Request $request)
    {
        $query = Veiculo::select('id', 'placa', 'marca', 'modelo', 'status');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('placa', 'like', "%{$request->search}%")
                  ->orWhere('marca', 'like', "%{$request->search}%")
                  ->orWhere('modelo', 'like', "%{$request->search}%");
            });
        }

        return response()->json($query->limit(50)->get());
    }
}
