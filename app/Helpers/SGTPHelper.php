<?php
namespace App\Helpers;

use Carbon\Carbon;

class SGTPHelper
{
    /**
     * Formatar valor monetário
     */
    public static function formatMoney($value)
    {
        return 'Kz ' . number_format($value, 2, ',', '.');
    }

    /**
     * Formatar data no padrão do sistema
     */
    public static function formatDate($date, $format = null)
    {
        if (!$date) return '-';

        $format = $format ?? config('sgtp.formato_data', 'd/m/Y');
        return Carbon::parse($date)->format($format);
    }

    /**
     * Formatar data e hora
     */
    public static function formatDateTime($date)
    {
        if (!$date) return '-';
        return Carbon::parse($date)->format(config('sgtp.formato_data_hora', 'd/m/Y H:i:s'));
    }

    /**
     * Gerar código único
     */
    public static function generateCode($prefix = '', $length = 8)
    {
        return $prefix . strtoupper(substr(uniqid() . bin2hex(random_bytes(5)), 0, $length));
    }

    /**
     * Calcular duração entre duas horas
     */
    public static function calcularDuracao($horaInicio, $horaFim)
    {
        $inicio = Carbon::parse($horaInicio);
        $fim = Carbon::parse($horaFim);

        $diff = $inicio->diff($fim);

        return $diff->format('%H:%I:%S');
    }

    /**
     * Obter status com cor
     */
    public static function getStatusBadge($status)
    {
        $colors = [
            'ATIVO' => 'success',
            'INATIVO' => 'danger',
            'MANUTENCAO' => 'warning',
            'PENDENTE' => 'warning',
            'EM_ANDAMENTO' => 'info',
            'FINALIZADA' => 'success',
            'CANCELADA' => 'danger',
            'ABERTA' => 'danger',
            'EM_ANALISE' => 'warning',
            'RESOLVIDA' => 'success',
            'VALIDO' => 'success',
            'UTILIZADO' => 'secondary',
            'EXPIRADO' => 'danger',
        ];

        $color = $colors[$status] ?? 'secondary';

        return "<span class='badge badge-{$color}'>{$status}</span>";
    }
}
