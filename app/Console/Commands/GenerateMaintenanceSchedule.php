<?php

namespace App\Console\Commands;

use App\Models\Veiculo;
use App\Models\Manutencao;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateMaintenanceSchedule extends Command
{
    protected $signature = 'sgtp:generate-maintenance';
    protected $description = 'Gera manutenções preventivas automaticamente (RN09)';

    public function handle()
    {
        $this->info('Gerando agendamentos de manutenção preventiva...');

        $veiculos = Veiculo::where('status', 'ATIVO')->get();
        $gerados = 0;

        foreach ($veiculos as $veiculo) {
            $ultimaManutencao = $veiculo->manutencoes()
                ->where('tipo', 'PREVENTIVA')
                ->where('status', 'CONCLUIDA')
                ->latest()
                ->first();

            $kmDesdeUltima = $veiculo->km_atual - ($ultimaManutencao ? $ultimaManutencao->created_at->km_atual ?? 0 : 0);
            $mesesDesdeUltima = $ultimaManutencao ? Carbon::parse($ultimaManutencao->data_fim)->diffInMonths(now()) : 999;

            // RN09: Manutenção preventiva a cada 10.000 km ou 6 meses
            if ($kmDesdeUltima >= 10000 || $mesesDesdeUltima >= 6) {
                $this->criarManutencaoPreventiva($veiculo);
                $gerados++;
            }
        }

        $this->info("{$gerados} manutenções preventivas agendadas.");
    }

    private function criarManutencaoPreventiva(Veiculo $veiculo)
    {
        Manutencao::create([
            'veiculo_id' => $veiculo->id,
            'tipo' => 'PREVENTIVA',
            'descricao' => 'Manutenção preventiva automática - Revisão completa',
            'data_agendamento' => now()->addDays(7),
            'oficina' => 'Oficina Credenciada',
            'custo_total' => 0,
            'status' => 'AGENDADA'
        ]);

        $this->line("Manutenção agendada para veículo {$veiculo->placa}");
    }
}
