<?php

namespace App\Services;

use App\Models\Escala;
use App\Models\ValidacaoBilhete;
use App\Models\Receita;
use App\Models\Despesa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Gerar relatório de cumprimento de horários
     */
    public function cumprimentoHorarios($dataInicio, $dataFim, $rotaId = null, $motoristaId = null)
    {
        $query = Escala::with(['rota', 'motorista'])
            ->whereBetween('data', [$dataInicio, $dataFim]);

        if ($rotaId) {
            $query->where('rota_id', $rotaId);
        }

        if ($motoristaId) {
            $query->where('motorista_id', $motoristaId);
        }

        $escalas = $query->get();

        $resultados = [];
        foreach ($escalas as $escala) {
            $atraso = null;
            $status = 'No horário';

            if ($escala->hora_inicio_real) {
                $atrasoSegundos = strtotime($escala->hora_inicio_real) - strtotime($escala->hora_inicio);
                if ($atrasoSegundos > 0) {
                    $status = $atrasoSegundos <= 600 ? 'Atraso < 10min' : 'Atraso > 15min';
                    $atraso = $atrasoSegundos;
                }
            }

            $resultados[] = [
                'data' => $escala->data,
                'rota' => $escala->rota->nome,
                'motorista' => $escala->motorista->nome_completo,
                'veiculo' => $escala->veiculo->placa,
                'hora_prevista' => $escala->hora_inicio,
                'hora_real' => $escala->hora_inicio_real ?? 'Não iniciada',
                'atraso' => $atraso ? gmdate('H:i:s', abs($atrasoSegundos)) : '0',
                'status' => $status,
            ];
        }

        return $resultados;
    }

    /**
     * Gerar relatório de desempenho de motoristas
     */
    public function desempenhoMotoristas($periodoInicio, $periodoFim, $motoristaId = null)
    {
        $query = Escala::with(['motorista'])
            ->whereBetween('data', [$periodoInicio, $periodoFim]);

        if ($motoristaId) {
            $query->where('motorista_id', $motoristaId);
        }

        $escalas = $query->get();

        $desempenho = [];
        foreach ($escalas->groupBy('motorista_id') as $motoristaId => $escalasMotorista) {
            $motorista = $escalasMotorista->first()->motorista;
            $totalKm = $escalasMotorista->sum(function ($e) {
                return $e->kmPercorrido() ?? 0;
            });

            $totalOcorrencias = \App\Models\Ocorrencia::where('colaborador_id', $motoristaId)
                ->whereBetween('data_ocorrencia', [$periodoInicio, $periodoFim])
                ->count();

            $totalViagens = $escalasMotorista->count();
            $viagensNoHorario = $escalasMotorista->filter(function ($e) {
                return $e->hora_inicio_real && $e->hora_inicio_real <= $e->hora_inicio;
            })->count();

            $taxaPontualidade = $totalViagens > 0 ? ($viagensNoHorario / $totalViagens) * 100 : 0;

            $desempenho[] = [
                'motorista' => $motorista->nome_completo,
                'total_viagens' => $totalViagens,
                'total_km' => $totalKm,
                'media_km_viagem' => $totalViagens > 0 ? round($totalKm / $totalViagens, 2) : 0,
                'taxa_pontualidade' => round($taxaPontualidade, 2),
                'ocorrencias' => $totalOcorrencias,
                'avaliacao' => $this->calcularAvaliacao($taxaPontualidade, $totalOcorrencias),
            ];
        }

        return $desempenho;
    }

    /**
     * Gerar relatório de fluxo de caixa
     */
    public function fluxoCaixa($dataInicio, $dataFim, $saldoInicial = 0)
    {
        $receitas = Receita::whereBetween('data', [$dataInicio, $dataFim])
            ->where('consolidado', true)
            ->sum('valor_total');

        $despesas = Despesa::whereBetween('data', [$dataInicio, $dataFim])
            ->where('aprovado', true)
            ->sum('valor');

        $saldoFinal = $saldoInicial + $receitas - $despesas;

        return [
            'periodo' => ['inicio' => $dataInicio, 'fim' => $dataFim],
            'saldo_inicial' => $saldoInicial,
            'total_receitas' => $receitas,
            'total_despesas' => $despesas,
            'saldo_final' => $saldoFinal,
            'resultado' => $receitas - $despesas,
        ];
    }

    private function calcularAvaliacao($taxaPontualidade, $ocorrencias)
    {
        $pontuacao = ($taxaPontualidade / 100) * 10;
        $pontuacao -= $ocorrencias * 0.5;

        if ($pontuacao >= 8) return 'Excelente';
        if ($pontuacao >= 6) return 'Bom';
        if ($pontuacao >= 4) return 'Regular';
        return 'Necessita Melhoria';
    }
}
