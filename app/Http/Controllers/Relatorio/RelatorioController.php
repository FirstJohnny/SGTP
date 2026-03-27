<?php

namespace App\Http\Controllers\Relatorio;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use App\Models\Escala;
use App\Models\Bilhete;
use App\Models\ValidacaoBilhete;
use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Ocorrencia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @method middleware(string|array $middleware, array $options = [])
 */

class RelatorioController extends Controller
{

    /**
     * Relatório de cumprimento de horários (RF35)
     */
    public function cumprimentoHorarios(Request $request)
    {
        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'rota_id' => 'nullable|exists:rota,id',
            'motorista_id' => 'nullable|exists:colaborador,id'
        ]);

        $query = Escala::with(['rota', 'motorista'])
            ->whereBetween('data', [$validated['data_inicio'], $validated['data_fim']]);

        if ($request->filled('rota_id')) {
            $query->where('rota_id', $validated['rota_id']);
        }

        if ($request->filled('motorista_id')) {
            $query->where('motorista_id', $validated['motorista_id']);
        }

        $escalas = $query->get();

        $dados = [];
        foreach ($escalas as $escala) {
            $atraso = null;
            $status = 'No horário';

            if ($escala->hora_inicio_real) {
                $atraso = strtotime($escala->hora_inicio_real) - strtotime($escala->hora_inicio);
                if ($atraso > 0) {
                    $status = $atraso <= 600 ? 'Atraso < 10min' : 'Atraso > 15min';
                }
            }

            $dados[] = [
                'data' => $escala->data,
                'rota' => $escala->rota->nome,
                'motorista' => $escala->motorista->nome_completo,
                'veiculo' => $escala->veiculo->placa,
                'hora_prevista' => $escala->hora_inicio,
                'hora_real' => $escala->hora_inicio_real ?? 'Não iniciada',
                'atraso' => $atraso ? gmdate('H:i:s', abs($atraso)) : '0',
                'status' => $status
            ];
        }

        if ($request->expectsJson()) {
            return response()->json($dados);
        }

        return view('relatorios.cumprimento', compact('dados'));
    }

    /**
     * Relatório de desempenho de motoristas (RF36)
     */
    public function desempenhoMotoristas(Request $request)
    {
        $validated = $request->validate([
            'periodo_inicio' => 'required|date',
            'periodo_fim' => 'required|date|after_or_equal:periodo_inicio',
            'motorista_id' => 'nullable|exists:colaborador,id'
        ]);

        $query = Escala::with(['motorista', 'veiculo'])
            ->whereBetween('data', [$validated['periodo_inicio'], $validated['periodo_fim']]);

        if ($request->filled('motorista_id')) {
            $query->where('motorista_id', $validated['motorista_id']);
        }

        $escalas = $query->get();

        $desempenho = [];
        foreach ($escalas->groupBy('motorista_id') as $motoristaId => $escalasMotorista) {
            $motorista = $escalasMotorista->first()->motorista;
            $totalKm = 0;
            $totalOcorrencias = Ocorrencia::where('colaborador_id', $motoristaId)
                ->whereBetween('data_ocorrencia', [$validated['periodo_inicio'], $validated['periodo_fim']])
                ->count();

            $totalViagens = 0;
            $viagensNoHorario = 0;

            foreach ($escalasMotorista as $escala) {
                $totalKm += $escala->kmPercorrido() ?? 0;
                $totalViagens++;

                if ($escala->hora_inicio_real && $escala->hora_inicio_real <= $escala->hora_inicio) {
                    $viagensNoHorario++;
                }
            }

            $taxaPontualidade = $totalViagens > 0 ? ($viagensNoHorario / $totalViagens) * 100 : 0;

            $desempenho[] = [
                'motorista' => $motorista->nome_completo,
                'total_viagens' => $totalViagens,
                'total_km' => $totalKm,
                'media_km_viagem' => $totalViagens > 0 ? round($totalKm / $totalViagens, 2) : 0,
                'taxa_pontualidade' => round($taxaPontualidade, 2),
                'ocorrencias' => $totalOcorrencias,
                'avaliacao' => $this->calcularAvaliacao($taxaPontualidade, $totalOcorrencias)
            ];
        }

        if ($request->expectsJson()) {
            return response()->json($desempenho);
        }

        return view('relatorios.desempenho', compact('desempenho'));
    }

    /**
     * Relatório de ocupação de veículos (RF37)
     */
    public function ocupacaoVeiculos(Request $request)
    {
        $validated = $request->validate([
            'periodo_inicio' => 'required|date',
            'periodo_fim' => 'required|date|after_or_equal:periodo_inicio',
            'rota_id' => 'nullable|exists:rota,id'
        ]);

        $query = ValidacaoBilhete::with(['bilhete.tarifa.rota', 'escala.veiculo'])
            ->whereBetween('timestamp', [$validated['periodo_inicio'], $validated['periodo_fim']]);

        $validacoes = $query->get();

        $ocupacao = [];
        $grupos = $request->filled('rota_id') ? $validacoes->groupBy('escala.veiculo.placa') : $validacoes->groupBy('bilhete.tarifa.rota.nome');

        foreach ($grupos as $key => $validacoesGrupo) {
            $totalPassageiros = $validacoesGrupo->count();
            $totalViagens = $validacoesGrupo->groupBy('escala_id')->count();

            $ocupacao[] = [
                'item' => $key,
                'total_passageiros' => $totalPassageiros,
                'total_viagens' => $totalViagens,
                'media_passageiros_viagem' => $totalViagens > 0 ? round($totalPassageiros / $totalViagens, 2) : 0
            ];
        }

        if ($request->expectsJson()) {
            return response()->json($ocupacao);
        }

        return view('relatorios.ocupacao', compact('ocupacao'));
    }

    /**
     * Fluxo de caixa (RF25)
     */
    public function fluxoCaixa(Request $request)
    {
        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'saldo_inicial' => 'nullable|numeric|min:0'
        ]);

        $receitas = Receita::whereBetween('data', [$validated['data_inicio'], $validated['data_fim']])
            ->where('consolidado', true)
            ->sum('valor_total');

        $despesas = Despesa::whereBetween('data', [$validated['data_inicio'], $validated['data_fim']])
            ->where('aprovado', true)
            ->sum('valor');

        $saldoInicial = $validated['saldo_inicial'] ?? 0;
        $saldoFinal = $saldoInicial + $receitas - $despesas;

        $dados = [
            'periodo' => [
                'inicio' => $validated['data_inicio'],
                'fim' => $validated['data_fim']
            ],
            'saldo_inicial' => $saldoInicial,
            'total_receitas' => $receitas,
            'total_despesas' => $despesas,
            'saldo_final' => $saldoFinal,
            'resultado' => $receitas - $despesas
        ];

        if ($request->expectsJson()) {
            return response()->json($dados);
        }

        return view('relatorios.fluxo-caixa', compact('dados'));
    }

    private function calcularAvaliacao(float $taxaPontualidade, int $ocorrencias): string
    {
        $pontuacao = ($taxaPontualidade / 100) * 10;
        $pontuacao -= $ocorrencias * 0.5;

        if ($pontuacao >= 8) {
            return 'Excelente';
        } elseif ($pontuacao >= 6) {
            return 'Bom';
        } elseif ($pontuacao >= 4) {
            return 'Regular';
        } else {
            return 'Necessita Melhoria';
        }
    }
}
