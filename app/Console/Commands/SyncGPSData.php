<?php

namespace App\Console\Commands;

use App\Models\Veiculo;
use App\Models\RastreamentoGps;
use App\Services\GPSIntegrationService;
use Illuminate\Console\Command;

class SyncGPSData extends Command
{
    protected $signature = 'sgtp:sync-gps';
    protected $description = 'Sincroniza dados de GPS dos veículos';

    public function handle(GPSIntegrationService $gpsService)
    {
        $this->info('Sincronizando dados GPS...');

        $veiculos = Veiculo::where('status', 'ATIVO')->get();
        $sincronizados = 0;

        foreach ($veiculos as $veiculo) {
            try {
                $dadosGPS = $gpsService->getVehiclePosition($veiculo->id);

                if ($dadosGPS) {
                    RastreamentoGps::create([
                        'veiculo_id' => $veiculo->id,
                        'latitude' => $dadosGPS['latitude'],
                        'longitude' => $dadosGPS['longitude'],
                        'velocidade' => $dadosGPS['velocidade'],
                        'direcao' => $dadosGPS['direcao'],
                        'ignicao' => $dadosGPS['ignicao'],
                        'timestamp' => now(),
                    ]);
                    $sincronizados++;
                }
            } catch (\Exception $e) {
                $this->error("Erro ao sincronizar veículo {$veiculo->placa}: {$e->getMessage()}");
            }
        }

        $this->info("{$sincronizados} veículos sincronizados.");
    }
}
