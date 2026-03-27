<?php
namespace Database\Seeders;

use App\Models\Permissao;
use Illuminate\Database\Seeder;

class PermissaoSeeder extends Seeder
{
    public function run()
    {
        $permissoes = [
            // Módulo Frota
            ['nome' => 'gerir_frota', 'descricao' => 'Gerenciar frota de veículos', 'modulo' => 'Frota'],
            ['nome' => 'ver_frota', 'descricao' => 'Visualizar frota de veículos', 'modulo' => 'Frota'],
            ['nome' => 'gerir_documentos', 'descricao' => 'Gerenciar documentos de veículos', 'modulo' => 'Frota'],
            ['nome' => 'gerir_abastecimentos', 'descricao' => 'Gerenciar abastecimentos', 'modulo' => 'Frota'],

            // Módulo Colaboradores
            ['nome' => 'gerir_colaboradores', 'descricao' => 'Gerenciar colaboradores', 'modulo' => 'Colaboradores'],
            ['nome' => 'ver_colaboradores', 'descricao' => 'Visualizar colaboradores', 'modulo' => 'Colaboradores'],
            ['nome' => 'gerir_ponto', 'descricao' => 'Gerenciar ponto dos colaboradores', 'modulo' => 'Colaboradores'],

            // Módulo Operações
            ['nome' => 'gerir_operacoes', 'descricao' => 'Gerenciar operações', 'modulo' => 'Operações'],
            ['nome' => 'gerir_rotas', 'descricao' => 'Gerenciar rotas', 'modulo' => 'Operações'],
            ['nome' => 'gerir_horarios', 'descricao' => 'Gerenciar horários', 'modulo' => 'Operações'],
            ['nome' => 'gerir_escalas', 'descricao' => 'Gerenciar escalas', 'modulo' => 'Operações'],

            // Módulo Bilhética
            ['nome' => 'gerir_bilhetica', 'descricao' => 'Gerenciar bilhética', 'modulo' => 'Bilhética'],
            ['nome' => 'gerir_tarifas', 'descricao' => 'Gerenciar tarifas', 'modulo' => 'Bilhética'],
            ['nome' => 'vender_bilhetes', 'descricao' => 'Vender bilhetes', 'modulo' => 'Bilhética'],
            ['nome' => 'validar_bilhetes', 'descricao' => 'Validar bilhetes', 'modulo' => 'Bilhética'],

            // Módulo Ocorrências
            ['nome' => 'gerir_ocorrencias', 'descricao' => 'Gerenciar ocorrências', 'modulo' => 'Ocorrências'],
            ['nome' => 'registrar_ocorrencias', 'descricao' => 'Registrar ocorrências', 'modulo' => 'Ocorrências'],

            // Módulo Financeiro
            ['nome' => 'gerir_financeiro', 'descricao' => 'Gerenciar financeiro', 'modulo' => 'Financeiro'],
            ['nome' => 'gerir_receitas', 'descricao' => 'Gerenciar receitas', 'modulo' => 'Financeiro'],
            ['nome' => 'gerir_despesas', 'descricao' => 'Gerenciar despesas', 'modulo' => 'Financeiro'],
            ['nome' => 'fechar_caixa', 'descricao' => 'Fechar caixa', 'modulo' => 'Financeiro'],

            // Módulo Relatórios
            ['nome' => 'gerar_relatorios', 'descricao' => 'Gerar relatórios', 'modulo' => 'Relatórios'],
            ['nome' => 'exportar_dados', 'descricao' => 'Exportar dados', 'modulo' => 'Relatórios'],

            // Módulo Admin
            ['nome' => 'gerir_usuarios', 'descricao' => 'Gerenciar usuários', 'modulo' => 'Administração'],
            ['nome' => 'gerir_perfis', 'descricao' => 'Gerenciar perfis de acesso', 'modulo' => 'Administração'],
            ['nome' => 'ver_auditoria', 'descricao' => 'Visualizar logs de auditoria', 'modulo' => 'Administração'],
            ['nome' => 'configurar_sistema', 'descricao' => 'Configurar sistema', 'modulo' => 'Administração'],

            // Módulo GPS
            ['nome' => 'ver_gps', 'descricao' => 'Visualizar rastreamento GPS', 'modulo' => 'GPS'],
            ['nome' => 'gerir_alertas', 'descricao' => 'Gerenciar alertas', 'modulo' => 'GPS'],
        ];

        foreach ($permissoes as $permissao) {
            Permissao::updateOrCreate(
                ['nome' => $permissao['nome']],
                $permissao
            );
        }

        $this->command->info('Permissões criadas com sucesso!');
    }
}
