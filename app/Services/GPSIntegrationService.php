<?php
// app/Services/GPSIntegrationService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GPSIntegrationService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.gps.api_url');
        $this->apiKey = config('services.gps.api_key');
    }

    /**
     * Obter posição de um veículo
     */
    public function getVehiclePosition($vehicleId)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get($this->apiUrl . '/vehicles/' . $vehicleId . '/position');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GPS Integration Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obter histórico de posições
     */
    public function getVehicleHistory($vehicleId, $startDate, $endDate)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get($this->apiUrl . '/vehicles/' . $vehicleId . '/history', [
                    'start' => $startDate,
                    'end' => $endDate,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('GPS History Error: ' . $e->getMessage());
            return [];
        }
    }
}
