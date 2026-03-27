<?php
namespace Database\Seeders;

use App\Models\Tarifa;
use App\Models\Rota;
use Illuminate\Database\Seeder;

class TarifaSeeder extends Seeder
{
    public function run()
    {
        $rotas = Rota::all();

        foreach ($rotas as $rota) {
            $tarifas = [
                [
                    'rota_id' => $rota->id,
                    'tipo_passageiro' => 'ADULTO',
                    'valor' => 150.00,
                    'data_inicio' => '2024-01-01',
                    'ativa' => true,
                ],
                [
                    'rota_id' => $rota->id,
                    'tipo_passageiro' => 'ESTUDANTE',
                    'valor' => 75.00,
                    'data_inicio' => '2024-01-01',
                    'ativa' => true,
                ],
                [
                    'rota_id' => $rota->id,
                    'tipo_passageiro' => 'IDOSO',
                    'valor' => 75.00,
                    'data_inicio' => '2024-01-01',
                    'ativa' => true,
                ],
            ];

            foreach ($tarifas as $tarifa) {
                Tarifa::updateOrCreate(
                    [
                        'rota_id' => $tarifa['rota_id'],
                        'tipo_passageiro' => $tarifa['tipo_passageiro']
                    ],
                    $tarifa
                );
            }
        }

        $this->command->info('Tarifas criadas com sucesso!');
    }
}
