<?php

namespace Database\Seeders;

use App\Models\Veiculo;
use App\Models\Colaborador;
use App\Models\Rota;
use App\Models\Escala;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EscalaSeeder extends Seeder
{
    public function run()
    {
        $veiculo = Veiculo::where('placa', 'LD-01-23')->first();
        $motorista = Colaborador::where('email', 'joao.chimuco@sgtp.ao')->first();
        $rota = Rota::where('codigo', 'L110')->first();
        
        if ($veiculo && $motorista && $rota) {
            Escala::create([
                'veiculo_id' => $veiculo->id,
                'motorista_id' => $motorista->id,
                'rota_id' => $rota->id,
                'data' => Carbon::today(),
                'hora_inicio' => '06:00:00',
                'hora_fim' => '14:00:00',
                'status' => 'PENDENTE'
            ]);
            
            $this->command->info('Escala criada para hoje!');
        }
    }
}