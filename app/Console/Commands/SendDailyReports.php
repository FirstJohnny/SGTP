<?php

namespace App\Console\Commands;

use App\Models\Receita;
use App\Models\Despesa;
use App\Models\Escala;
use App\Mail\DailyReportMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDailyReports extends Command
{
    protected $signature = 'sgtp:send-reports';
    protected $description = 'Envia relatórios diários por e-mail';

    public function handle()
    {
        $this->info('Enviando relatórios diários...');

        $data = Carbon::yesterday();

        $dados = [
            'data' => $data->format('d/m/Y'),
            'receitas' => Receita::whereDate('data', $data)->sum('valor_total'),
            'despesas' => Despesa::whereDate('data', $data)->sum('valor'),
            'escalas_realizadas' => Escala::whereDate('data', $data)->where('status', 'FINALIZADA')->count(),
            'passageiros' => \App\Models\ValidacaoBilhete::whereDate('timestamp', $data)->count(),
        ];

        // Enviar para gestores
        $gestores = \App\Models\User::whereIn('tipo_usuario', ['ADMIN', 'GESTOR_OPERACOES', 'FINANCEIRO'])
            ->where('status', 'ATIVO')
            ->get();

        foreach ($gestores as $gestor) {
            if ($gestor->email) {
                Mail::to($gestor->email)->send(new DailyReportMail($dados));
            }
        }

        $this->info('Relatórios enviados com sucesso!');
    }
}
