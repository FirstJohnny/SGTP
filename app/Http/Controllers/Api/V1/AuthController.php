<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login via API
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        if ($user->status !== 'ATIVO') {
            return response()->json(['message' => 'Usuário inativo'], 403);
        }

        // Gerar token Sanctum
        $deviceName = $request->device_name ?? $request->userAgent() ?? 'unknown';
        $token = $user->createToken($deviceName)->plainTextToken;

        // Registrar log
        LogAuditoria::registrar(
            $user->id,
            'API_LOGIN',
            'users',
            $user->id,
            null,
            ['ip' => $request->ip(), 'device' => $deviceName]
        );

        // Atualizar último acesso
        $user->ultimo_acesso = now();
        $user->save();

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Logout via API
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Revogar token atual
            $request->user()->currentAccessToken()->delete();

            LogAuditoria::registrar(
                $user->id,
                'API_LOGOUT',
                'users',
                $user->id,
                null,
                ['ip' => $request->ip()]
            );
        }

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    /**
     * Obter dados do usuário autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['perfilAcesso', 'colaborador']);

        return response()->json($user);
    }

    /**
     * Atualizar perfil do usuário
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:150',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'telefone' => 'sometimes|string|max:20',
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dadosAntigos = $user->toArray();

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('telefone')) {
            $user->telefone = $request->telefone;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        LogAuditoria::registrar(
            $user->id,
            'API_UPDATE_PROFILE',
            'users',
            $user->id,
            $dadosAntigos,
            $user->toArray()
        );

        return response()->json(['message' => 'Perfil atualizado com sucesso', 'user' => $user]);
    }
}
