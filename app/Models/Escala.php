<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escala extends Model
{
    protected $table = 'escala';

    protected $fillable = [
        'veiculo_id',
        'motorista_id',
        'cobrador_id',
        'rota_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'hora_inicio_real',
        'hora_fim_real',
        'km_inicial',
        'km_final',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'data' => 'date',
        'hora_inicio' => 'string',
        'hora_fim' => 'string',
        'hora_inicio_real' => 'string',
        'hora_fim_real' => 'string',
        'km_inicial' => 'integer',
        'km_final' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para status
    const STATUS_PENDENTE = 'PENDENTE';
    const STATUS_EM_ANDAMENTO = 'EM_ANDAMENTO';
    const STATUS_FINALIZADA = 'FINALIZADA';
    const STATUS_CANCELADA = 'CANCELADA';

    /**
     * Relacionamento com veículo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Relacionamento com motorista
     */
    public function motorista(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'motorista_id');
    }

    /**
     * Relacionamento com cobrador
     */
    public function cobrador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'cobrador_id');
    }

    /**
     * Relacionamento com rota
     */
    public function rota(): BelongsTo
    {
        return $this->belongsTo(Rota::class, 'rota_id');
    }

    /**
     * Relacionamento com validações de bilhete
     */
    public function validacoes(): HasMany
    {
        return $this->hasMany(ValidacaoBilhete::class, 'escala_id');
    }

    /**
     * Relacionamento com rastreamentos GPS
     */
    public function rastreamentos(): HasMany
    {
        return $this->hasMany(RastreamentoGps::class, 'escala_id');
    }

    /**
     * Verificar se escala está em andamento
     */
    public function isEmAndamento(): bool
    {
        return $this->status === self::STATUS_EM_ANDAMENTO;
    }

    /**
     * Iniciar escala
     */
    public function iniciar(int $kmInicial = null)
    {
        $this->status = self::STATUS_EM_ANDAMENTO;
        $this->hora_inicio_real = now()->format('H:i:s');

        if ($kmInicial) {
            $this->km_inicial = $kmInicial;
        }

        $this->save();
    }

    /**
     * Finalizar escala
     */
    public function finalizar(int $kmFinal = null)
    {
        $this->status = self::STATUS_FINALIZADA;
        $this->hora_fim_real = now()->format('H:i:s');

        if ($kmFinal) {
            $this->km_final = $kmFinal;
        }

        $this->save();
    }

    /**
     * Calcular quilometragem percorrida
     */
    public function kmPercorrido(): ?int
    {
        if ($this->km_inicial && $this->km_final) {
            return $this->km_final - $this->km_inicial;
        }

        return null;
    }
}
