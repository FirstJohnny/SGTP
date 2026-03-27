<?php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\User;
use App\Models\Alerta;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Enviar notificação para usuários
     */
    public function send($userIds, $title, $message, $type = 'info')
    {
        $users = User::whereIn('id', (array)$userIds)->get();

        foreach ($users as $user) {
            // Criar notificação no banco
            $user->notifications()->create([
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'read_at' => null,
            ]);

            // Enviar e-mail se configurado
            if ($user->email) {
                Mail::raw($message, function ($mail) use ($user, $title) {
                    $mail->to($user->email)
                        ->subject('SGTP - ' . $title);
                });
            }
        }
    }

    /**
     * Criar alerta para veículo
     */
    public function createAlert($veiculoId, $tipo, $gravidade, $mensagem)
    {
        return Alerta::create([
            'veiculo_id' => $veiculoId,
            'tipo' => $tipo,
            'gravidade' => $gravidade,
            'mensagem' => $mensagem,
            'timestamp' => now(),
            'resolvido' => false,
        ]);
    }
}
