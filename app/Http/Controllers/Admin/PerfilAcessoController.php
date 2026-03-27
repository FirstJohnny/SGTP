<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerfilAcesso;
use App\Models\Permissao;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class PerfilAcessoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->tipo_usuario !== 'ADMIN') {
                abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
            }
            return $next($request);
        });
    }

    /**
     * Listar perfis de acesso
     */
    public function index(Request $request)
    {
        $query = PerfilAcesso::query();

        if ($request->filled('search')) {
            $query->where('nome', 'like', "%{$request->search}%");
        }

        $perfis = $query->orderBy('nome')->paginate(15);

        return view('admin.perfis.index', compact('perfis'));
    }

    /**
     * Formulário de cadastro
     */
    public function create()
    {
        $permissoes = Permissao::orderBy('modulo')->orderBy('nome')->get();
        $permissoesPorModulo = $permissoes->groupBy('modulo');

        return view('admin.perfis.create', compact('permissoesPorModulo'));
    }

    /**
     * Cadastrar perfil
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:perfil_acesso',
            'descricao' => 'nullable|string',
            'permissoes' => 'array',
            'permissoes.*' => 'exists:permissao,id'
        ]);

        DB::beginTransaction();
        try {
            $perfil = PerfilAcesso::create([
                'nome' => $validated['nome'],
                'descricao' => $validated['descricao']
            ]);

            if (!empty($validated['permissoes'])) {
                $perfil->permissoes()->sync($validated['permissoes']);
            }

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'perfil_acesso',
                $perfil->id,
                null,
                $perfil->toArray()
            );

            DB::commit();

            return redirect()->route('admin.perfis.index')
                ->with('success', 'Perfil criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar perfil: ' . $e->getMessage());
        }
    }

    /**
     * Exibir detalhes do perfil
     */
    public function show(PerfilAcesso $perfil)
    {
        $permissoes = $perfil->permissoes()->orderBy('modulo')->get();
        $usuarios = $perfil->usuarios()->limit(10)->get();

        return view('admin.perfis.show', compact('perfil', 'permissoes', 'usuarios'));
    }

    /**
     * Formulário de edição
     */
    public function edit(PerfilAcesso $perfil)
    {
        $permissoes = Permissao::orderBy('modulo')->orderBy('nome')->get();
        $permissoesPorModulo = $permissoes->groupBy('modulo');
        $permissoesSelecionadas = $perfil->permissoes()->pluck('id')->toArray();

        return view('admin.perfis.edit', compact('perfil', 'permissoesPorModulo', 'permissoesSelecionadas'));
    }

    /**
     * Atualizar perfil
     */
    public function update(Request $request, PerfilAcesso $perfil)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:perfil_acesso,nome,' . $perfil->id,
            'descricao' => 'nullable|string',
            'permissoes' => 'array',
            'permissoes.*' => 'exists:permissao,id'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $perfil->toArray();

            $perfil->update([
                'nome' => $validated['nome'],
                'descricao' => $validated['descricao']
            ]);

            $perfil->permissoes()->sync($validated['permissoes'] ?? []);

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'perfil_acesso',
                $perfil->id,
                $dadosAntigos,
                $perfil->toArray()
            );

            DB::commit();

            return redirect()->route('admin.perfis.index')
                ->with('success', 'Perfil atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }

    /**
     * Remover perfil
     */
    public function destroy(PerfilAcesso $perfil)
    {
        // Verificar se há usuários vinculados
        if ($perfil->usuarios()->count() > 0) {
            return back()->with('error', 'Não é possível remover perfil com usuários vinculados.');
        }

        DB::beginTransaction();
        try {
            $dados = $perfil->toArray();
            $perfil->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'perfil_acesso',
                $perfil->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('admin.perfis.index')
                ->with('success', 'Perfil removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover perfil: ' . $e->getMessage());
        }
    }

    /**
     * Sincronizar permissões
     */
    public function syncPermissoes(Request $request, PerfilAcesso $perfil)
    {
        $validated = $request->validate([
            'permissoes' => 'array',
            'permissoes.*' => 'exists:permissao,id'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $perfil->permissoes()->pluck('id')->toArray();
            $perfil->permissoes()->sync($validated['permissoes'] ?? []);

            LogAuditoria::registrar(
                Auth::id(),
                'SYNC_PERMISSOES',
                'perfil_acesso',
                $perfil->id,
                ['permissoes' => $dadosAntigos],
                ['permissoes' => $validated['permissoes'] ?? []]
            );

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
