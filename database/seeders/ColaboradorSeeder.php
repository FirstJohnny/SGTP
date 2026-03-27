<?php
namespace Database\Seeders;

use App\Models\Colaborador;
use Illuminate\Database\Seeder;

class ColaboradorSeeder extends Seeder
{
    public function run()
    {
        $colaboradores = [
            [
                'tipo' => 'MOTORISTA',
                'nome_completo' => 'João Chimuco',
                'bi' => '001234567LA001',
                'numero_carta' => 'C-123456',
                'carta_validade' => '2026-12-31',
                'categoria_carta' => 'D',
                'data_contratacao' => '2023-01-15',
                'salario_base' => 85000.00,
                'numero_seguranca_social' => 'NIF-123456789',
                'telefone' => '+244 923 456 789',
                'email' => 'joao.chimuco@sgtp.ao',
                'emergencia_contato' => '+244 923 456 788',
            ],
            [
                'tipo' => 'COBRADOR',
                'nome_completo' => 'Maria Kativa',
                'bi' => '001234568LA002',
                'data_contratacao' => '2023-02-01',
                'salario_base' => 45000.00,
                'numero_seguranca_social' => 'NIF-123456788',
                'telefone' => '+244 923 456 780',
                'email' => 'maria.kativa@sgtp.ao',
                'emergencia_contato' => '+244 923 456 779',
            ],
            [
                'tipo' => 'FISCAL',
                'nome_completo' => 'António Lukamba',
                'bi' => '001234569LA003',
                'data_contratacao' => '2023-03-10',
                'salario_base' => 65000.00,
                'numero_seguranca_social' => 'NIF-123456787',
                'telefone' => '+244 923 456 777',
                'email' => 'antonio.lukamba@sgtp.ao',
                'emergencia_contato' => '+244 923 456 776',
            ],
        ];

        foreach ($colaboradores as $colaborador) {
            Colaborador::updateOrCreate(
                ['bi' => $colaborador['bi']],
                $colaborador
            );
        }

        $this->command->info('Colaboradores criados com sucesso!');
    }
}
