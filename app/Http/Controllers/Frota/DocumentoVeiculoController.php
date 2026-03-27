<?php

namespace App\Http\Controllers\Frota;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\DocumentoVeiculo;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class DocumentoVeiculoController extends Controller
{

    /**
     * Listar documentos do veículo
     */
    public function index(Veiculo $veiculo)
    {
        $documentos = $veiculo->documentos()->latest()->paginate(10);
        return view('frota.documentos.index', compact('veiculo', 'documentos'));
    }

    /**
     * Formulário para adicionar documento
     */
    public function create(Veiculo $veiculo)
    {
        return view('frota.documentos.create', compact('veiculo'));
    }

    /**
     * Adicionar documento ao veículo
     */
    public function store(Request $request, Veiculo $veiculo)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:LICENCA,SEGURO,INSPECAO,REGISTO,OUTRO',
            'numero_documento' => 'required|string|max:50',
            'data_emissao' => 'required|date',
            'data_validade' => 'required|date|after:data_emissao',
            'arquivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'observacoes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $dados = [
                'veiculo_id' => $veiculo->id,
                'tipo' => $validated['tipo'],
                'numero_documento' => $validated['numero_documento'],
                'data_emissao' => $validated['data_emissao'],
                'data_validade' => $validated['data_validade'],
                'observacoes' => $validated['observacoes'] ?? null
            ];

            if ($request->hasFile('arquivo')) {
                $path = $request->file('arquivo')->store('documentos/' . $veiculo->id, 'public');
                $dados['arquivo_url'] = Storage::url($path);
            }

            $documento = DocumentoVeiculo::create($dados);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'documento_veiculo',
                $documento->id,
                null,
                $dados
            );

            DB::commit();

            return redirect()->route('frota.documentos.index', $veiculo)
                ->with('success', 'Documento adicionado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao adicionar documento: ' . $e->getMessage());
        }
    }

    /**
     * Exibir detalhes do documento
     */
    public function show(DocumentoVeiculo $documento)
    {
        return view('frota.documentos.show', compact('documento'));
    }

    /**
     * Remover documento
     */
    public function destroy(DocumentoVeiculo $documento)
    {
        DB::beginTransaction();
        try {
            $dados = $documento->toArray();
            $veiculoId = $documento->veiculo_id;

            // Remover arquivo se existir
            if ($documento->arquivo_url) {
                $path = str_replace('/storage/', '', $documento->arquivo_url);
                Storage::disk('public')->delete($path);
            }

            $documento->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'documento_veiculo',
                $documento->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('frota.documentos.index', $veiculoId)
                ->with('success', 'Documento removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover documento: ' . $e->getMessage());
        }
    }

    /**
     * Verificar documentos com validade próxima do vencimento
     */
    public function expiracao()
    {
        $documentos = DocumentoVeiculo::where('data_validade', '<=', now()->addDays(30))
            ->where('data_validade', '>=', now())
            ->with('veiculo')
            ->orderBy('data_validade')
            ->get();

        return view('frota.documentos.expiracao', compact('documentos'));
    }
}
