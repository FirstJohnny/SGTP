<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'telefone' => 'nullable|string|max:20',
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telefone' => $validated['telefone'] ?? $user->telefone,
        ]);
        
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }
        
        return redirect()->route('profile')->with('success', 'Perfil atualizado com sucesso!');
    }
}