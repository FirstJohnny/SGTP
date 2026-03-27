<?php

return [
    // Limites de horas de condução (RN14)
    'limite_horas_conducao' => env('LIMITE_HORAS_CONDUCAO', 8),

    // Km para manutenção preventiva (RN09)
    'km_manutencao_preventiva' => env('KM_MANUTENCAO_PREVENTIVA', 10000),

    // Meses para manutenção preventiva
    'meses_manutencao_preventiva' => env('MESES_MANUTENCAO_PREVENTIVA', 6),

    // Alerta de vencimento de documentos (dias)
    'alerta_vencimento_dias' => env('ALERTA_VENCIMENTO_DIAS', 30),

    // Tempo máximo de atraso para alerta (minutos - RN10)
    'alerta_atraso_minutos' => env('ALERTA_ATRASO_MINUTOS', 15),

    // Validade do bilhete em dias
    'bilhete_validade_dias' => env('BILHETE_VALIDADE_DIAS', 30),

    // Tempo de validação do bilhete (segundos)
    'bilhete_tempo_validador' => env('BILHETE_TEMPO_VALIDADOR', 30),

    // Configurações de GPS
    'gps_atualizacao_segundos' => env('GPS_ATUALIZACAO_SEGUNDOS', 30),
    'gps_desvio_raio_metros' => env('GPS_DESVIO_RAIO_METROS', 500),

    // Configurações de notificação
    'notificacao_email' => env('NOTIFICACAO_EMAIL', true),
    'notificacao_sms' => env('NOTIFICACAO_SMS', false),

    // Configurações de segurança
    '2fa_obrigatorio' => env('2FA_OBRIGATORIO', false),
    'senha_expiracao_dias' => env('SENHA_EXPIRACAO_DIAS', 90),
    'tentativas_login' => env('TENTATIVAS_LOGIN', 5),

    // Configurações de backup
    'backup_automatico' => env('BACKUP_AUTOMATICO', true),
    'backup_retencao_dias' => env('BACKUP_RETENCAO_DIAS', 30),

    // Formato de data
    'formato_data' => env('FORMATO_DATA', 'd/m/Y'),
    'formato_data_hora' => env('FORMATO_DATA_HORA', 'd/m/Y H:i:s'),
];
