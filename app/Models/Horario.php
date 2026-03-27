<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $table = 'horario';

    protected $fillable = [
        'rota_id',
        'hora_partida',
        'hora_chegada',
        'dias_semana',
        'tipo_dia',
        'ativo'
    ];

    protected $casts = [
        'hora_partida' => 'string',
        'hora_chegada' => 'string',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipo dia
    const TIPO_NORMAL = 'NORMAL';
    const TIPO_FERIADO = 'FERIADO';
    const TIPO_ESPECIAL = 'ESPECIAL';

    /**
     * Relacionamento com rota
     */
    public function rota(): BelongsTo
    {
        return $this->belongsTo(Rota::class, 'rota_id');
    }

    /**
     * Verificar se horário está ativo para um dia específico
     */
    public function isAtivoParaDia(string $diaSemana, string $tipoDia): bool
    {
        if (!$this->ativo) {
            return false;
        }

        $diasArray = explode(',', $this->dias_semana);

        return in_array($diaSemana, $diasArray) && $this->tipo_dia === $tipoDia;
    }
}
