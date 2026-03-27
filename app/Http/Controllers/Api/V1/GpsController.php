<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\RastreamentoGps;
use App\Models\Escala;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpsController extends Controller
{
    /**
     * Atualizar posição do veículo
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'velocidade' => 'nullable|numeric|min:0',
            'direcao' => 'nullable|integer|min:0|max:359',
            'ignicao' => 'boolean',
            'odometro' => 'nullable|integer|min:0',
            'precisao' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Buscar escala ativa do veículo
            $escala = Escala::where('veiculo_id', $validated['veiculo_id'])
                ->where('status', Escala::STATUS_EM_ANDAMENTO)
                ->with('rota') // Carregar a rota com relacionamento
                ->first();

            $rastreamento = RastreamentoGps::create([
                'veiculo_id' => $validated['veiculo_id'],
                'escala_id' => $escala?->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'velocidade' => $validated['velocidade'] ?? null,
                'direcao' => $validated['direcao'] ?? null,
                'ignicao' => $validated['ignicao'] ?? false,
                'odometro_gps' => $validated['odometro'] ?? null,
                'timestamp' => now(),
                'precisao' => $validated['precisao'] ?? null
            ]);

            // Verificar desvio de rota (apenas se tiver escala ativa)
            if ($escala && $escala->rota) {
                $this->verificarDesvioRota($rastreamento, $escala);
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => $rastreamento]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar veículos ativos
     */
    public function vehicles()
    {
        $veiculos = Veiculo::where('status', Veiculo::STATUS_ATIVO)
            ->select('id', 'placa', 'marca', 'modelo', 'cor')
            ->get();

        // Adicionar última posição
        foreach ($veiculos as $veiculo) {
            $veiculo->ultima_posicao = RastreamentoGps::where('veiculo_id', $veiculo->id)
                ->latest('timestamp')
                ->first(['latitude', 'longitude', 'velocidade', 'timestamp']);
        }

        return response()->json($veiculos);
    }

    /**
     * Posição de um veículo específico
     */
    public function vehicle($id)
    {
        $veiculo = Veiculo::with(['ultimoRastreamento'])->findOrFail($id);

        return response()->json([
            'veiculo' => $veiculo,
            'posicao' => $veiculo->ultimoRastreamento
        ]);
    }

    /**
     * Histórico de percurso (RF17)
     */
    public function historico(Request $request)
    {
        $validated = $request->validate([
            'veiculo_id' => 'required|exists:veiculo,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio'
        ]);

        $rastreamentos = RastreamentoGps::where('veiculo_id', $validated['veiculo_id'])
            ->whereBetween('timestamp', [$validated['data_inicio'], $validated['data_fim']])
            ->orderBy('timestamp')
            ->get(['latitude', 'longitude', 'velocidade', 'timestamp']);

        return response()->json($rastreamentos);
    }

    /**
     * Verificar desvio de rota
     * @param RastreamentoGps $rastreamento
     * @param Escala $escala
     */
    private function verificarDesvioRota(RastreamentoGps $rastreamento, Escala $escala)
    {
        // Verificar se a rota tem trajeto definido
        $rota = $escala->rota;
        
        if (!$rota) {
            return;
        }

        // Obter o trajeto - pode ser string (JSON) ou array (após cast)
        $trajetoGeoJson = $rota->trajeto_geojson;
        
        // Se for string, decodificar para array
        if (is_string($trajetoGeoJson)) {
            $trajeto = json_decode($trajetoGeoJson, true);
        } else {
            $trajeto = $trajetoGeoJson;
        }

        // Verificar se é um array válido
        if (!is_array($trajeto)) {
            return;
        }

        // Verificar se está dentro do raio permitido (500m)
        $dentro = false;
        $raioPermitido = config('sgtp.gps_desvio_raio_metros', 500);
        
        // Tentar obter as coordenadas de diferentes estruturas possíveis
        $coordenadas = $this->extrairCoordenadasDoTrajeto($trajeto);
        
        foreach ($coordenadas as $coordenada) {
            $distancia = $this->calcularDistancia(
                (float)$rastreamento->latitude, 
                (float)$rastreamento->longitude,
                (float)$coordenada['lat'], 
                (float)$coordenada['lng']
            );
            if ($distancia <= $raioPermitido) {
                $dentro = true;
                break;
            }
        }

        // Gerar alerta se estiver fora da rota
        if (!$dentro) {
            // Verificar se já existe um alerta recente para este veículo (evitar spam)
            $alertaRecente = Alerta::where('veiculo_id', $rastreamento->veiculo_id)
                ->where('tipo', Alerta::TIPO_GPS)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();
                
            if (!$alertaRecente) {
                Alerta::create([
                    'veiculo_id' => $rastreamento->veiculo_id,
                    'tipo' => Alerta::TIPO_GPS,
                    'gravidade' => Alerta::GRAVIDADE_MEDIA,
                    'mensagem' => "Veículo se desviou da rota planeada",
                    'latitude' => $rastreamento->latitude,
                    'longitude' => $rastreamento->longitude,
                    'timestamp' => now(),
                    'resolvido' => false
                ]);
            }
        }
    }

    /**
     * Extrair coordenadas de diferentes formatos de GeoJSON
     * @param array $trajeto
     * @return array
     */
    private function extrairCoordenadasDoTrajeto(array $trajeto): array
    {
        $coordenadas = [];
        
        // Formato GeoJSON padrão: {"type":"LineString","coordinates":[[lng,lat],...]}
        if (isset($trajeto['type']) && $trajeto['type'] === 'LineString' && isset($trajeto['coordinates'])) {
            foreach ($trajeto['coordinates'] as $coord) {
                if (isset($coord[0], $coord[1])) {
                    $coordenadas[] = ['lng' => $coord[0], 'lat' => $coord[1]];
                }
            }
        }
        // Formato alternativo: array direto de pontos
        elseif (isset($trajeto['points']) && is_array($trajeto['points'])) {
            foreach ($trajeto['points'] as $point) {
                if (isset($point['lat'], $point['lng'])) {
                    $coordenadas[] = ['lat' => $point['lat'], 'lng' => $point['lng']];
                }
            }
        }
        // Formato simples: array de arrays com lat/lng
        elseif (is_array($trajeto) && isset($trajeto[0]['lat'])) {
            foreach ($trajeto as $point) {
                if (isset($point['lat'], $point['lng'])) {
                    $coordenadas[] = ['lat' => $point['lat'], 'lng' => $point['lng']];
                }
            }
        }
        // Formato simples: array de arrays com [lng, lat]
        elseif (is_array($trajeto) && isset($trajeto[0][0])) {
            foreach ($trajeto as $coord) {
                if (isset($coord[0], $coord[1])) {
                    $coordenadas[] = ['lng' => $coord[0], 'lat' => $coord[1]];
                }
            }
        }
        
        return $coordenadas;
    }

    /**
     * Calcular distância entre dois pontos (fórmula de Haversine)
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distância em metros
     */
    private function calcularDistancia(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // metros

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}