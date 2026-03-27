<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Rota;
use App\Models\Horario;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class PublicController extends Controller
{
    /**
     * Rotas disponíveis (API pública)
     */
    public function rotas()
    {
        $rotas = Rota::where('ativa', true)
            ->select('id', 'nome', 'codigo', 'tipo', 'distancia_total')
            ->get();

        return response()->json($rotas);
    }

    /**
     * Horários por rota (API pública)
     */
    public function horarios(Request $request)
    {
        $validated = $request->validate([
            'rota_id' => 'required|exists:rota,id',
            'data' => 'nullable|date'
        ]);

        $data = isset($validated['data']) ? Carbon::parse($validated['data']) : Carbon::today();
        $diaSemana = strtoupper(substr($data->format('D'), 0, 3));
        $tipoDia = $data->isHoliday() ? Horario::TIPO_FERIADO : Horario::TIPO_NORMAL;

        $horarios = Horario::where('rota_id', $validated['rota_id'])
            ->where('ativo', true)
            ->where(function($q) use ($diaSemana, $tipoDia) {
                $q->where('dias_semana', 'like', "%{$diaSemana}%")
                  ->orWhere('tipo_dia', $tipoDia);
            })
            ->orderBy('hora_partida')
            ->get(['hora_partida', 'hora_chegada']);

        return response()->json($horarios);
    }

    /**
     * Receber feedback de passageiros (RF41)
     */
    public function feedback(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:150',
            'tipo' => 'required|in:ELOGIO,SUGESTAO,RECLAMACAO,DUVIDA',
            'mensagem' => 'required|string|max:1000',
            'rota_id' => 'nullable|exists:rota,id',
            'veiculo_id' => 'nullable|exists:veiculo,id'
        ]);

        $feedback = Feedback::create([
            'nome' => $validated['nome'] ?? 'Anônimo',
            'email' => $validated['email'] ?? null,
            'tipo' => $validated['tipo'],
            'mensagem' => $validated['mensagem'],
            'rota_id' => $validated['rota_id'] ?? null,
            'veiculo_id' => $validated['veiculo_id'] ?? null,
            'ip_address' => $request->ip(),
            'data_envio' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback enviado com sucesso! Obrigado pela sua contribuição.'
        ]);
    }

    /**
     * Planejar viagem (consulta pública)
     */
    public function planejarViagem(Request $request)
    {
        $validated = $request->validate([
            'origem_lat' => 'required|numeric|between:-90,90',
            'origem_lng' => 'required|numeric|between:-180,180',
            'destino_lat' => 'required|numeric|between:-90,90',
            'destino_lng' => 'required|numeric|between:-180,180',
            'data' => 'nullable|date'
        ]);

        // Buscar rotas próximas à origem e destino
        // Esta é uma implementação simplificada
        $rotas = Rota::where('ativa', true)
            ->with(['pontosParagem' => function($q) {
                $q->select('id', 'nome', 'latitude', 'longitude');
            }])
            ->get();

        $sugestoes = [];
        foreach ($rotas as $rota) {
            foreach ($rota->pontosParagem as $ponto) {
                $distanciaOrigem = $this->calcularDistancia(
                    $validated['origem_lat'], $validated['origem_lng'],
                    $ponto->latitude, $ponto->longitude
                );

                $distanciaDestino = $this->calcularDistancia(
                    $validated['destino_lat'], $validated['destino_lng'],
                    $ponto->latitude, $ponto->longitude
                );

                if ($distanciaOrigem <= 1000 && $distanciaDestino <= 1000) {
                    $sugestoes[] = [
                        'rota' => $rota->nome,
                        'codigo' => $rota->codigo,
                        'ponto_proximo' => $ponto->nome,
                        'distancia_origem' => round($distanciaOrigem),
                        'distancia_destino' => round($distanciaDestino)
                    ];
                    break;
                }
            }
        }

        return response()->json($sugestoes);
    }

    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
