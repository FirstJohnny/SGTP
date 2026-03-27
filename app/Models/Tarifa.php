<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarifa extends Model
{
    protected $table = 'tarifa';

    protected $fillable = [
        'rota_id',
        'tipo_passageiro',
        'valor',
        'data_inicio',
        'data_fim',
        'ativa'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativa' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipos de passageiro
    const TIPO_ADULTO = 'ADULTO';
    const TIPO_ESTUDANTE = 'ESTUDANTE';
    const TIPO_IDOSO = 'IDOSO';
    const TIPO_OUTRO = 'OUTRO';

    /**
     * Relacionamento com rota
     */
    public function rota(): BelongsTo
    {
        return $this->belongsTo(Rota::class, 'rota_id');
    }

    /**
     * Relacionamento com bilhetes
     */
    public function bilhetes(): HasMany
    {
        return $this->hasMany(Bilhete::class, 'tarifa_id');
    }

    /**
     * Verificar se tarifa está vigente
     */
    public function isVigente(): bool
    {
        if (!$this->ativa) {
            return false;
        }

        $hoje = now();

        return $this->data_inicio <= $hoje && (!$this->data_fim || $this->data_fim >= $hoje);
    }
}
