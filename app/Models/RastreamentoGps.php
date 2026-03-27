<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RastreamentoGps extends Model
{
    protected $table = 'rastreamento_gps';

    protected $fillable = [
        'veiculo_id',
        'escala_id',
        'latitude',
        'longitude',
        'velocidade',
        'direcao',
        'ignicao',
        'odometro_gps',
        'timestamp',
        'precisao'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'velocidade' => 'float',
        'direcao' => 'integer',
        'ignicao' => 'boolean',
        'odometro_gps' => 'integer',
        'timestamp' => 'datetime',
        'precisao' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com veículo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Relacionamento com escala
     */
    public function escala(): BelongsTo
    {
        return $this->belongsTo(Escala::class, 'escala_id');
    }

    /**
     * Verificar se veículo está em movimento
     */
    public function isEmMovimento(): bool
    {
        return $this->velocidade > 5 && $this->ignicao;
    }
}
