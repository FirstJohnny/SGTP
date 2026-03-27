<?php

namespace Database\Seeders;

use App\Models\Veiculo;
use App\Models\Manutencao;
use Illuminate\Database\Seeder;

class ManutencaoSeeder extends Seeder
{
    public function run()
    {
        $veiculo = Veiculo::where('placa', 'HU-42-XX')->first();
        
        if ($veiculo) {
            Manutencao::create([
                'veiculo_id' => $veiculo->id,
                'tipo' => 'PREVENTIVA',
                'descricao' => 'Revisão de 80.000 km - Troca de óleo e filtros',
                'data_agendamento' => now()->addDays(7),
                'oficina' => 'Oficina Central',
                'custo_total' => 25000,
                'status' => 'AGENDADA'
            ]);
            
            $this->command->info('Manutenção preventiva criada para veículo HU-42-XX!');
        }
    }
}