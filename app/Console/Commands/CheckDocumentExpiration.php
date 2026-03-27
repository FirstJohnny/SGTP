<?php

namespace App\Console\Commands;

use App\Models\Veiculo;
use App\Models\Colaborador;
use App\Models\Alerta;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckDocumentExpiration extends Command
{
    protected $signature = 'sgtp:check-documents';
    protected $description = 'Verifica documentos expirados ou próximos do vencimento';

    public function handle()
    {
        $this->info('Verificando documentos...');

        // Verificar documentos de veículos (RN01)
        $veiculos = Veiculo::where('seguro_validade', '<', Carbon::now()->addDays(30))->get();

        foreach ($veiculos as $veiculo) {
            if ($veiculo->seguro_validade < Carbon::now()) {
                $this->criarAlerta($veiculo, 'SEGURO', 'Documento de seguro expirado!');
            } else {
                $this->criarAlerta($veiculo, 'SEGURO', 'Seguro vence em ' . Carbon::now()->diffInDays($veiculo->seguro_validade) . ' dias', 'LEVE');
            }
        }

        // Verificar carteiras de motoristas (RN02)
        $motoristas = Colaborador::where('tipo', 'MOTORISTA')
            ->where('carta_validade', '<', Carbon::now()->addDays(30))
            ->get();

        foreach ($motoristas as $motorista) {
            if ($motorista->carta_validade < Carbon::now()) {
                $this->criarAlertaMotorista($motorista, 'CARTA', 'Carteira de motorista expirada!', 'GRAVE');
            } else {
                $this->criarAlertaMotorista($motorista, 'CARTA', 'Carteira vence em ' . Carbon::now()->diffInDays($motorista->carta_validade) . ' dias', 'MEDIA');
            }
        }

        $this->info('Verificação concluída!');
    }

    private function criarAlerta($veiculo, $tipo, $mensagem, $gravidade = 'MEDIA')
    {
        Alerta::create([
            'veiculo_id' => $veiculo->id,
            'tipo' => $tipo,
            'gravidade' => $gravidade,
            'mensagem' => $mensagem,
            'timestamp' => now(),
            'resolvido' => false
        ]);
    }

    private function criarAlertaMotorista($motorista, $tipo, $mensagem, $gravidade = 'MEDIA')
    {
        // Criar alerta para o motorista
        // Pode ser expandido para notificações no sistema
        $this->warn("Motorista {$motorista->nome_completo}: {$mensagem}");
    }
}
