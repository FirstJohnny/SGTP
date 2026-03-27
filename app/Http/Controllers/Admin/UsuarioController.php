<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PerfilAcesso;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Verificar se o usuário é admin
     */
    private function checkAdmin()
    {
        if (!Auth::check() || Auth::user()->tipo_usuario !== 'ADMIN') {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
        }
    }

    /**
     * Listar usuários
     */
    public function index(Request $request)
    {
        $this->checkAdmin();
        
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('bi', 'like', "%{$request->search}%");
        }

        if ($request->filled('tipo_usuario')) {
            $query->where('tipo_usuario', $request->tipo_usuario);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);
        $perfis = PerfilAcesso::all();

        $stats = [
            'total' => User::count(),
            'ativos' => User::where('status', 'ATIVO')->count(),
            'bloqueados' => User::where('status', 'BLOQUEADO')->count(),
            'com_2fa' => User::where('two_factor_enabled', true)->count(),
        ];

        return view('admin.usuarios.index', compact('usuarios', 'perfis', 'stats'));
    }

    /**
     * Show form to create a new user
     */
    public function create()
    {
        $this->checkAdmin();
        
        $perfis = PerfilAcesso::all();
        
        return view('admin.usuarios.create', compact('perfis'));
    }

    /**
     * Criar usuário
     */
    public function store(Request $request)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users',
            'bi' => 'nullable|string|max:20|unique:users',
            'telefone' => 'nullable|string|max:20',
            'tipo_usuario' => 'required|in:ADMIN,GESTOR_OPERACOES,GESTOR_FROTA,FISCAL,OPERADOR_BILHETICA,FINANCEIRO',
            'perfil_acesso_id' => 'nullable|exists:perfil_acesso,id',
            'status' => 'required|in:ATIVO,INATIVO,BLOQUEADO',
            'password' => 'required|string|min:8|confirmed'
        ]);

        DB::beginTransaction();
        try {
            $usuario = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'bi' => $validated['bi'],
                'telefone' => $validated['telefone'],
                'tipo_usuario' => $validated['tipo_usuario'],
                'perfil_acesso_id' => $validated['perfil_acesso_id'],
                'status' => $validated['status'],
                'password' => Hash::make($validated['password'])
            ]);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'users',
                $usuario->id,
                null,
                $usuario->toArray()
            );

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $usuario)
    {
        $this->checkAdmin();
        
        return view('admin.usuarios.show', compact('usuario'));
    }

    /**
     * Show form to edit user
     */
    public function edit(User $usuario)
    {
        $this->checkAdmin();
        
        $perfis = PerfilAcesso::all();
        
        return view('admin.usuarios.edit', compact('usuario', 'perfis'));
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, User $usuario)
    {
        $this->checkAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,' . $usuario->id,
            'bi' => 'nullable|string|max:20|unique:users,bi,' . $usuario->id,
            'telefone' => 'nullable|string|max:20',
            'tipo_usuario' => 'required|in:ADMIN,GESTOR_OPERACOES,GESTOR_FROTA,FISCAL,OPERADOR_BILHETICA,FINANCEIRO',
            'perfil_acesso_id' => 'nullable|exists:perfil_acesso,id',
            'status' => 'required|in:ATIVO,INATIVO,BLOQUEADO',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $usuario->toArray();

            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $usuario->update($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'users',
                $usuario->id,
                $dadosAntigos,
                $usuario->toArray()
            );

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function destroy(User $usuario)
    {
        $this->checkAdmin();
        
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }
        
        DB::beginTransaction();
        try {
            $dados = $usuario->toArray();
            $usuario->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'users',
                $usuario->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover usuário: ' . $e->getMessage());
        }
    }

    /**
     * Bloquear usuário
     */
    public function bloquear(User $usuario)
    {
        $this->checkAdmin();
        
        DB::beginTransaction();
        try {
            $dadosAntigos = $usuario->toArray();
            $usuario->update(['status' => 'BLOQUEADO']);

            LogAuditoria::registrar(
                Auth::id(),
                'BLOQUEAR_USUARIO',
                'users',
                $usuario->id,
                $dadosAntigos,
                $usuario->toArray()
            );

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário bloqueado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao bloquear usuário: ' . $e->getMessage());
        }
    }
    
    /**
     * Ativar usuário
     */
    public function ativar(User $usuario)
    {
        $this->checkAdmin();
        
        DB::beginTransaction();
        try {
            $dadosAntigos = $usuario->toArray();
            $usuario->update(['status' => 'ATIVO']);

            LogAuditoria::registrar(
                Auth::id(),
                'ATIVAR_USUARIO',
                'users',
                $usuario->id,
                $dadosAntigos,
                $usuario->toArray()
            );

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário ativado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao ativar usuário: ' . $e->getMessage());
        }
    }
}