<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PontoRota extends Model
{
    protected $table = 'ponto_rota';

    protected $fillable = [
        'rota_id',
        'ponto_paragem_id',
        'ordem',
        'tempo_estimado_chegada',
        'distancia_desde_inicio'
    ];

    protected $casts = [
        'ordem' => 'integer',
        'tempo_estimado_chegada' => 'integer',
        'distancia_desde_inicio' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com rota
     */
    public function rota(): BelongsTo
    {
        return $this->belongsTo(Rota::class, 'rota_id');
    }

    /**
     * Relacionamento com ponto de paragem
     */
    public function pontoParagem(): BelongsTo
    {
        return $this->belongsTo(PontoParagem::class, 'ponto_paragem_id');
    }
}
