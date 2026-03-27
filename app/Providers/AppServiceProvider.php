<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // <-- ADICIONAR
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Validadores customizados
        Validator::extend('cpf', function ($attribute, $value, $parameters, $validator) {
            return $this->validateCpf($value);
        });

        Validator::extend('placa_veiculo', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[A-Z]{3}[0-9]{4}$/', $value);
        });

        Validator::extend('telefone_angola', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(\+244|00244)?[9][0-9]{8}$/', $value);
        });

        Validator::extend('bi_angola', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]{6,14}[A-Z]{2}[0-9]{3}$/', $value);
        });

        // Directivas Blade
        Blade::if('role', function ($role) {
            if (!Auth::check()) {
                return false;
            }
            return Auth::user()->tipo_usuario === $role;
        });

        Blade::if('permission', function ($permission) {
            if (!Auth::check()) {
                return false;
            }
            
            // Verificar se o método hasPermissao existe no modelo User
            if (method_exists(Auth::user(), 'hasPermissao')) {
                return Auth::user()->hasPermissao($permission);
            }
            
            // Fallback: admin tem todas as permissões
            if (Auth::user()->tipo_usuario === 'ADMIN') {
                return true;
            }
            
            return false;
        });
        
        // Directiva para verificar se está logado
        Blade::if('auth', function () {
            return Auth::check();
        });
        
        // Directiva para verificar se é admin
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->tipo_usuario === 'ADMIN';
        });
    }

    /**
     * Validar CPF brasileiro (pode ser usado para documentos em Angola também)
     */
    private function validateCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        // Validação simples de CPF
        $invalidos = ['00000000000', '11111111111', '22222222222', '33333333333',
                      '44444444444', '55555555555', '66666666666', '77777777777',
                      '88888888888', '99999999999'];

        if (in_array($cpf, $invalidos)) {
            return false;
        }

        // Cálculo dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}