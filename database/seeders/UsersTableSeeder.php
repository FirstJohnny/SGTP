<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PerfilAcesso;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar perfis de acesso
        $perfilAdmin = PerfilAcesso::where('nome', 'Administrador')->first();
        $perfilOperacoes = PerfilAcesso::where('nome', 'Gestor Operações')->first();
        $perfilFrota = PerfilAcesso::where('nome', 'Gestor Frota')->first();
        $perfilFiscal = PerfilAcesso::where('nome', 'Fiscal')->first();
        $perfilOperador = PerfilAcesso::where('nome', 'Operador Bilhética')->first();
        $perfilFinanceiro = PerfilAcesso::where('nome', 'Financeiro')->first();

        // Usuário Administrador
        User::updateOrCreate(
            ['email' => 'admin@sgtp.ao'],
            [
                'name' => 'Administrador SGTP',
                'email' => 'admin@sgtp.ao',
                'password' => Hash::make('admin123'),
                'tipo_usuario' => 'ADMIN',
                'status' => 'ATIVO',
                'bi' => '000000001LA001',
                'telefone' => '+244 900 000 001',
                'perfil_acesso_id' => $perfilAdmin ? $perfilAdmin->id : null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Usuário Gestor de Operações
        User::updateOrCreate(
            ['email' => 'operacoes@sgtp.ao'],
            [
                'name' => 'Gestor de Operações',
                'email' => 'operacoes@sgtp.ao',
                'password' => Hash::make('operacoes123'),
                'tipo_usuario' => 'GESTOR_OPERACOES',
                'status' => 'ATIVO',
                'bi' => '000000002LA002',
                'telefone' => '+244 900 000 002',
                'perfil_acesso_id' => $perfilOperacoes ? $perfilOperacoes->id : null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Usuário Gestor de Frota
        User::updateOrCreate(
            ['email' => 'frota@sgtp.ao'],
            [
                'name' => 'Gestor de Frota',
                'email' => 'frota@sgtp.ao',
                'password' => Hash::make('frota123'),
                'tipo_usuario' => 'GESTOR_FROTA',
                'status' => 'ATIVO',
                'bi' => '000000003LA003',
                'telefone' => '+244 900 000 003',
                'perfil_acesso_id' => $perfilFrota ? $perfilFrota->id : null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Usuário Fiscal
        User::updateOrCreate(
            ['email' => 'fiscal@sgtp.ao'],
            [
                'name' => 'Fiscal de Transportes',
                'email' => 'fiscal@sgtp.ao',
                'password' => Hash::make('fiscal123'),
                'tipo_usuario' => 'FISCAL',
                'status' => 'ATIVO',
                'bi' => '000000004LA004',
                'telefone' => '+244 900 000 004',
                'perfil_acesso_id' => $perfilFiscal ? $perfilFiscal->id : null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Usuário Operador de Bilhética
        User::updateOrCreate(
            ['email' => 'bilheteiro@sgtp.ao'],
            [
                'name' => 'Operador de Bilhética',
                'email' => 'bilheteiro@sgtp.ao',
                'password' => Hash::make('bilhete123'),
                'tipo_usuario' => 'OPERADOR_BILHETICA',
                'status' => 'ATIVO',
                'bi' => '000000005LA005',
                'telefone' => '+244 900 000 005',
                'perfil_acesso_id' => $perfilOperador ? $perfilOperador->id : null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Usuário Financeiro
        User::updateOrCreate(
            ['email' => 'financeiro@sgtp.ao'],
            [
                'name' => 'Departamento Financeiro',
                'email' => 'financeiro@sgtp.ao',
                'password' => Hash::make('financeiro123'),
                'tipo_usuario' => 'FINANCEIRO',
                'status' => 'ATIVO',
                'bi' => '000000006LA006',
                'telefone' => '+244 900 000 006',
                'perfil_acesso_id' => $perfilFinanceiro ? $perfilFinanceiro->id : null,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $this->command->info('Usuários criados com sucesso!');
        $this->command->info('Credenciais:');
        $this->command->info('Admin: admin@sgtp.ao / admin123');
        $this->command->info('Operações: operacoes@sgtp.ao / operacoes123');
        $this->command->info('Frota: frota@sgtp.ao / frota123');
        $this->command->info('Fiscal: fiscal@sgtp.ao / fiscal123');
        $this->command->info('Bilheteiro: bilheteiro@sgtp.ao / bilhete123');
        $this->command->info('Financeiro: financeiro@sgtp.ao / financeiro123');
    }
}