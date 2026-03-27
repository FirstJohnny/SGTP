<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan; 

class ConfiguracaoController extends Controller
{

    /**
     * Exibir configurações do sistema
     */
    public function index()
    {
        // Carregar configurações do banco ou arquivo de configuração
        $configuracoes = [
            // Configurações Gerais
            'sistema_nome' => config('app.name', 'SGTP'),
            'sistema_email' => config('mail.from.address'),
            'fuso_horario' => config('app.timezone'),
            'formato_data' => config('sgtp.formato_data', 'd/m/Y'),

            // Configurações Operacionais
            'limite_horas_conducao' => config('sgtp.limite_horas_conducao', 8),
            'km_manutencao_preventiva' => config('sgtp.km_manutencao_preventiva', 10000),
            'meses_manutencao_preventiva' => config('sgtp.meses_manutencao_preventiva', 6),
            'alerta_vencimento_dias' => config('sgtp.alerta_vencimento_dias', 30),
            'alerta_atraso_minutos' => config('sgtp.alerta_atraso_minutos', 15),

            // Configurações de Bilhética
            'bilhete_validade_dias' => config('sgtp.bilhete_validade_dias', 30),
            'bilhete_tempo_validador' => config('sgtp.bilhete_tempo_validador', 30),

            // Configurações de GPS
            'gps_atualizacao_segundos' => config('sgtp.gps_atualizacao_segundos', 30),
            'gps_desvio_raio_metros' => config('sgtp.gps_desvio_raio_metros', 500),

            // Configurações de Notificações
            'notificacao_email' => config('sgtp.notificacao_email', true),
            'notificacao_sms' => config('sgtp.notificacao_sms', false),

            // Configurações de Segurança
            '2fa_obrigatorio' => config('sgtp.2fa_obrigatorio', false),
            'senha_expiracao_dias' => config('sgtp.senha_expiracao_dias', 90),
            'tentativas_login' => config('sgtp.tentativas_login', 5),

            // Configurações de Backup
            'backup_automatico' => config('sgtp.backup_automatico', true),
            'backup_retencao_dias' => config('sgtp.backup_retencao_dias', 30),
        ];

        return view('admin.configuracoes.index', compact('configuracoes'));
    }

    /**
     * Atualizar configurações
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'sistema_nome' => 'required|string|max:100',
            'sistema_email' => 'required|email',
            'fuso_horario' => 'required|string',
            'formato_data' => 'required|string',
            'limite_horas_conducao' => 'required|integer|min:1|max:12',
            'km_manutencao_preventiva' => 'required|integer|min:1000|max:50000',
            'meses_manutencao_preventiva' => 'required|integer|min:1|max:24',
            'alerta_vencimento_dias' => 'required|integer|min:1|max:90',
            'alerta_atraso_minutos' => 'required|integer|min:1|max:60',
            'bilhete_validade_dias' => 'required|integer|min:1|max:365',
            'gps_atualizacao_segundos' => 'required|integer|min:10|max:300',
            'gps_desvio_raio_metros' => 'required|integer|min:100|max:2000',
            'notificacao_email' => 'boolean',
            'notificacao_sms' => 'boolean',
            '2fa_obrigatorio' => 'boolean',
            'tentativas_login' => 'required|integer|min:1|max:10',
            'backup_automatico' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $configuracoesAntigas = $this->getCurrentConfig();

            // Atualizar configurações no arquivo .env
            $this->updateEnvFile([
                'APP_NAME' => $validated['sistema_nome'],
                'APP_TIMEZONE' => $validated['fuso_horario'],
                'MAIL_FROM_ADDRESS' => $validated['sistema_email'],
            ]);

            // Salvar configurações customizadas no cache ou banco
            $configuracoesParaSalvar = [
                'formato_data' => $validated['formato_data'],
                'limite_horas_conducao' => $validated['limite_horas_conducao'],
                'km_manutencao_preventiva' => $validated['km_manutencao_preventiva'],
                'meses_manutencao_preventiva' => $validated['meses_manutencao_preventiva'],
                'alerta_vencimento_dias' => $validated['alerta_vencimento_dias'],
                'alerta_atraso_minutos' => $validated['alerta_atraso_minutos'],
                'bilhete_validade_dias' => $validated['bilhete_validade_dias'],
                'gps_atualizacao_segundos' => $validated['gps_atualizacao_segundos'],
                'gps_desvio_raio_metros' => $validated['gps_desvio_raio_metros'],
                'notificacao_email' => $validated['notificacao_email'],
                'notificacao_sms' => $validated['notificacao_sms'],
                '2fa_obrigatorio' => $validated['2fa_obrigatorio'],
                'tentativas_login' => $validated['tentativas_login'],
                'backup_automatico' => $validated['backup_automatico'],
            ];

            Cache::forever('sgtp_configuracoes', $configuracoesParaSalvar);

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE_CONFIGURACOES',
                'configuracoes',
                null,
                $configuracoesAntigas,
                $validated
            );

            DB::commit();

            return back()->with('success', 'Configurações atualizadas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar configurações: ' . $e->getMessage());
        }
    }

    /**
     * Gerenciar backup
     */
    public function backup(Request $request)
    {
        $tipo = $request->query('tipo', $request->input('tipo', 'database'));
        
        $request->validate([
            'tipo' => 'required|in:database,storage,full'
        ]);

        try {
            // Criar diretório de backups se não existir
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $filename = 'backup_' . $tipo . '_' . now()->format('Ymd_His') . '.zip';
            $filepath = $backupDir . '/' . $filename;

            // Criar arquivo ZIP
            $zip = new \ZipArchive();
            if ($zip->open($filepath, \ZipArchive::CREATE) !== true) {
                throw new \Exception('Não foi possível criar o arquivo de backup');
            }

            // Executar backup baseado no tipo
            switch ($tipo) {
                case 'database':
                    $this->backupDatabaseToZip($zip);
                    break;
                case 'storage':
                    $this->backupStorageToZip($zip);
                    break;
                case 'full':
                    $this->backupDatabaseToZip($zip);
                    $this->backupStorageToZip($zip);
                    break;
            }

            $zip->close();

            LogAuditoria::registrar(
                Auth::id(),
                'BACKUP',
                'configuracoes',
                null,
                null,
                ['tipo' => $tipo, 'filename' => $filename]
            );

            return response()->download($filepath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar backup: ' . $e->getMessage());
        }
    }

    /**
     * Backup do banco de dados
     */
    private function backupDatabaseToZip($zip)
    {
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        
        // Criar arquivo SQL temporário
        $tempSql = storage_path('app/backups/temp_' . uniqid() . '.sql');
        
        // Comando mysqldump
        $command = sprintf(
            'mysqldump --user="%s" --password="%s" --host="%s" "%s" > "%s" 2>&1',
            $dbUser,
            $dbPass,
            $dbHost,
            $dbName,
            $tempSql
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Erro ao fazer dump do banco de dados: ' . implode("\n", $output));
        }
        
        if (file_exists($tempSql) && filesize($tempSql) > 0) {
            $zip->addFile($tempSql, 'database.sql');
            unlink($tempSql);
        } else {
            throw new \Exception('Arquivo SQL vazio ou não gerado');
        }
    }

    /**
     * Backup dos arquivos de storage
     */
    private function backupStorageToZip($zip)
    {
        $storagePath = storage_path('app/public');
        
        if (!file_exists($storagePath)) {
            // Se a pasta não existir, criar uma vazia
            mkdir($storagePath, 0755, true);
            return;
        }
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($storagePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = 'storage/' . substr($filePath, strlen($storagePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
