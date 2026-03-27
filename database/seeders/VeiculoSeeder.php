<?php
namespace Database\Seeders;

use App\Models\Veiculo;
use Illuminate\Database\Seeder;

class VeiculoSeeder extends Seeder
{
    public function run()
    {
        $veiculos = [
            [
                'placa' => 'LD-01-23',
                'chassi' => '9BWZZZ377VT004251',
                'marca' => 'Mercedes-Benz',
                'modelo' => 'Urban 3000',
                'ano_fabricado' => 2022,
                'cor' => 'Azul',
                'lotacao' => 50,
                'tipo_combustivel' => 'DIESEL',
                'consumo_medio' => 5.2,
                'km_atual' => 45000,
                'data_aquisicao' => '2022-03-15',
                'status' => 'ATIVO',
                'seguro_validade' => '2025-12-31',
                'proxima_inspecao' => '2025-04-15',
            ],
            [
                'placa' => 'LB-87-HA',
                'chassi' => '9BWZZZ377VT004252',
                'marca' => 'Scania',
                'modelo' => 'K360',
                'ano_fabricado' => 2023,
                'cor' => 'Branco',
                'lotacao' => 45,
                'tipo_combustivel' => 'DIESEL',
                'consumo_medio' => 4.8,
                'km_atual' => 28000,
                'data_aquisicao' => '2023-01-20',
                'status' => 'ATIVO',
                'seguro_validade' => '2026-01-20',
                'proxima_inspecao' => '2025-05-10',
            ],
            [
                'placa' => 'HU-42-XX',
                'chassi' => '9BWZZZ377VT004253',
                'marca' => 'Volvo',
                'modelo' => 'B8R',
                'ano_fabricado' => 2021,
                'cor' => 'Amarelo',
                'lotacao' => 55,
                'tipo_combustivel' => 'DIESEL',
                'consumo_medio' => 5.5,
                'km_atual' => 78000,
                'data_aquisicao' => '2021-08-10',
                'status' => 'MANUTENCAO',
                'seguro_validade' => '2025-08-10',
                'proxima_inspecao' => '2025-03-01',
            ],
        ];

        foreach ($veiculos as $veiculo) {
            Veiculo::updateOrCreate(
                ['placa' => $veiculo['placa']],
                $veiculo
            );
        }

        $this->command->info('Veículos criados com sucesso!');
    }
}
