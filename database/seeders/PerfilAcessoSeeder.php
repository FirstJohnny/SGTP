<?php

namespace Database\Seeders;

use App\Models\PerfilAcesso;
use App\Models\Permissao;
use Illuminate\Database\Seeder;

class PerfilAcessoSeeder extends Seeder
{
    public function run()
    {
        // Buscar todas as permissões
        $todasPermissoes = Permissao::pluck('id')->toArray();
        
        // Perfil Administrador (todas as permissões)
        $perfilAdmin = PerfilAcesso::updateOrCreate(
            ['nome' => 'Administrador'],
            ['descricao' => 'Acesso total ao sistema']
        );
        $perfilAdmin->permissoes()->sync($todasPermissoes);
        
        // Perfil Gestor Operações
        $permissoesOperacoes = Permissao::whereIn('nome', [
            'ver_dashboard', 'gerir_operacoes', 'gerir_rotas', 'gerir_horarios', 
            'gerir_escalas', 'ver_frota', 'ver_colaboradores', 'gerar_relatorios'
        ])->pluck('id')->toArray();
        
        $perfilOperacoes = PerfilAcesso::updateOrCreate(
            ['nome' => 'Gestor Operações'],
            ['descricao' => 'Gerencia rotas, horários e escalas']
        );
        $perfilOperacoes->permissoes()->sync($permissoesOperacoes);
        
        // Perfil Gestor Frota
        $permissoesFrota = Permissao::whereIn('nome', [
            'ver_dashboard', 'gerir_frota', 'ver_frota', 'gerir_abastecimentos', 
            'gerir_documentos', 'gerir_manutencoes', 'gerar_relatorios'
        ])->pluck('id')->toArray();
        
        $perfilFrota = PerfilAcesso::updateOrCreate(
            ['nome' => 'Gestor Frota'],
            ['descricao' => 'Gerencia veículos e manutenções']
        );
        $perfilFrota->permissoes()->sync($permissoesFrota);
        
        // Perfil Fiscal
        $permissoesFiscal = Permissao::whereIn('nome', [
            'ver_dashboard', 'ver_frota', 'ver_colaboradores', 
            'registrar_ocorrencias', 'validar_bilhetes', 'gerar_relatorios'
        ])->pluck('id')->toArray();
        
        $perfilFiscal = PerfilAcesso::updateOrCreate(
            ['nome' => 'Fiscal'],
            ['descricao' => 'Fiscaliza operações e ocorrências']
        );
        $perfilFiscal->permissoes()->sync($permissoesFiscal);
        
        // Perfil Operador Bilhética
        $permissoesOperador = Permissao::whereIn('nome', [
            'ver_dashboard', 'vender_bilhetes', 'validar_bilhetes', 'gerar_relatorios'
        ])->pluck('id')->toArray();
        
        $perfilOperador = PerfilAcesso::updateOrCreate(
            ['nome' => 'Operador Bilhética'],
            ['descricao' => 'Vende e valida bilhetes']
        );
        $perfilOperador->permissoes()->sync($permissoesOperador);
        
        // Perfil Financeiro
        $permissoesFinanceiro = Permissao::whereIn('nome', [
            'ver_dashboard', 'gerir_financeiro', 'gerir_receitas', 
            'gerir_despesas', 'fechar_caixa', 'gerar_relatorios'
        ])->pluck('id')->toArray();
        
        $perfilFinanceiro = PerfilAcesso::updateOrCreate(
            ['nome' => 'Financeiro'],
            ['descricao' => 'Acesso a relatórios financeiros']
        );
        $perfilFinanceiro->permissoes()->sync($permissoesFinanceiro);
        
        $this->command->info('Perfis de acesso criados com permissões!');
    }
}