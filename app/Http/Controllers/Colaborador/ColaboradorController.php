<?php

namespace App\Http\Controllers\Colaborador;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use App\Models\User;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */
class ColaboradorController extends Controller
{

    /**
     * Listar colaboradores
     */
    public function index(Request $request)
    {
        $query = Colaborador::query();

        if ($request->filled('search')) {
            $query->where('nome_completo', 'like', "%{$request->search}%")
                  ->orWhere('bi', 'like', "%{$request->search}%")
                  ->orWhere('telefone', 'like', "%{$request->search}%");
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            if ($request->status === 'ATIVO') {
                $query->whereNull('data_demissao');
            } else {
                $query->whereNotNull('data_demissao');
            }
        }

        $colaboradores = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('colaboradores.index', compact('colaboradores'));
    }

    /**
     * Formulário de cadastro
     */
    public function create()
    {
        return view('colaboradores.create');
    }

    /**
     * Cadastrar colaborador
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:MOTORISTA,COBRADOR,FISCAL,OUTRO',
            'nome_completo' => 'required|string|max:150',
            'bi' => 'required|string|max:20|unique:colaborador',
            'numero_carta' => 'nullable|required_if:tipo,MOTORISTA|string|max:50',
            'carta_validade' => 'nullable|required_if:tipo,MOTORISTA|date|after:today',
            'categoria_carta' => 'nullable|required_if:tipo,MOTORISTA|string|max:20',
            'data_contratacao' => 'required|date',
            'data_demissao' => 'nullable|date|after:data_contratacao',
            'salario_base' => 'required|numeric|min:0',
            'numero_seguranca_social' => 'required|string|max:30',
            'telefone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'foto_url' => 'nullable|url|max:255',
            'emergencia_contato' => 'required|string|max:100',
            'criar_usuario' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $colaborador = Colaborador::create($validated);

            // Criar usuário do sistema se solicitado
            if ($request->boolean('criar_usuario') && $request->filled('email')) {
                $user = User::create([
                    'name' => $validated['nome_completo'],
                    'email' => $validated['email'],
                    'password' => Hash::make('password123'), // Senha temporária
                    'bi' => $validated['bi'],
                    'telefone' => $validated['telefone'],
                    'tipo_usuario' => $this->mapearTipoUsuario($validated['tipo']),
                    'status' => User::STATUS_ATIVO
                ]);
                $colaborador->user_id = $user->id;
                $colaborador->save();
            }

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'colaborador',
                $colaborador->id,
                null,
                $colaborador->toArray()
            );

            DB::commit();

            return redirect()->route('colaboradores.index')
                ->with('success', 'Colaborador cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar colaborador: ' . $e->getMessage());
        }
    }

    /**
     * Exibir detalhes do colaborador
     */
    public function show(Colaborador $colaborador)
    {
        $escalas = $colaborador->escalasMotorista()
            ->with(['veiculo', 'rota'])
            ->orderBy('data', 'desc')
            ->limit(20)
            ->get();

        $ocorrencias = $colaborador->ocorrencias()
            ->with('veiculo')
            ->orderBy('data_ocorrencia', 'desc')
            ->limit(20)
            ->get();

        return view('colaboradores.show', compact('colaborador', 'escalas', 'ocorrencias'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Colaborador $colaborador)
    {
        return view('colaboradores.edit', compact('colaborador'));
    }

    /**
     * Atualizar colaborador
     */
    public function update(Request $request, Colaborador $colaborador)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:MOTORISTA,COBRADOR,FISCAL,OUTRO',
            'nome_completo' => 'required|string|max:150',
            'bi' => 'required|string|max:20|unique:colaborador,bi,' . $colaborador->id,
            'numero_carta' => 'nullable|required_if:tipo,MOTORISTA|string|max:50',
            'carta_validade' => 'nullable|required_if:tipo,MOTORISTA|date',
            'categoria_carta' => 'nullable|required_if:tipo,MOTORISTA|string|max:20',
            'data_contratacao' => 'required|date',
            'data_demissao' => 'nullable|date|after:data_contratacao',
            'salario_base' => 'required|numeric|min:0',
            'numero_seguranca_social' => 'required|string|max:30',
            'telefone' => 'required|string|max:20',
            'email' => 'nullable|email|max:150',
            'foto_url' => 'nullable|url|max:255',
            'emergencia_contato' => 'required|string|max:100'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $colaborador->toArray();
            $colaborador->update($validated);

            // Atualizar usuário associado se existir
            if ($colaborador->user_id && $request->filled('email')) {
                $colaborador->usuario->update([
                    'name' => $validated['nome_completo'],
                    'email' => $validated['email'],
                    'bi' => $validated['bi'],
                    'telefone' => $validated['telefone'],
                    'tipo_usuario' => $this->mapearTipoUsuario($validated['tipo'])
                ]);
            }

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'colaborador',
                $colaborador->id,
                $dadosAntigos,
                $colaborador->toArray()
            );

            DB::commit();

            return redirect()->route('colaboradores.index')
                ->with('success', 'Colaborador atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar colaborador: ' . $e->getMessage());
        }
    }

    /**
     * Remover colaborador (soft delete)
     */
    public function destroy(Colaborador $colaborador)
    {
        DB::beginTransaction();
        try {
            $dados = $colaborador->toArray();

            // Desativar usuário associado
            if ($colaborador->user_id) {
                $colaborador->usuario->update(['status' => User::STATUS_INATIVO]);
            }

            $colaborador->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'colaborador',
                $colaborador->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('colaboradores.index')
                ->with('success', 'Colaborador removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover colaborador: ' . $e->getMessage());
        }
    }

    /**
     * API: Listar colaboradores para selects
     */
    public function list(Request $request)
    {
        $query = Colaborador::select('id', 'nome_completo', 'tipo');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('search')) {
            $query->where('nome_completo', 'like', "%{$request->search}%");
        }

        $query->whereNull('data_demissao');

        return response()->json($query->limit(50)->get());
    }

    private function mapearTipoUsuario($tipoColaborador)
    {
        $map = [
            'MOTORISTA' => 'MOTORISTA',
            'FISCAL' => 'FISCAL',
            'COBRADOR' => 'OPERADOR_BILHETICA'
        ];

        return $map[$tipoColaborador] ?? 'OPERADOR_BILHETICA';
    }
}
