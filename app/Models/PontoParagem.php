<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PontoParagem extends Model
{
    use SoftDeletes;

    protected $table = 'ponto_paragem';

    protected $fillable = [
        'nome',
        'latitude',
        'longitude',
        'endereco',
        'tipo',
        'tem_abrigo',
        'tem_bilheteira'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'tem_abrigo' => 'boolean',
        'tem_bilheteira' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_PONTO = 'PONTO';
    const TIPO_TERMINAL = 'TERMINAL';
    const TIPO_OUTRO = 'OUTRO';

    /**
     * Relacionamento com rotas
     */
    public function rotas(): BelongsToMany
    {
        return $this->belongsToMany(Rota::class, 'ponto_rota', 'ponto_paragem_id', 'rota_id')
                    ->withPivot('ordem', 'tempo_estimado_chegada', 'distancia_desde_inicio')
                    ->orderBy('pivot_ordem');
    }

    /**
     * Relacionamento com ponto_rota
     */
    public function pontosRota()
    {
        return $this->hasMany(PontoRota::class, 'ponto_paragem_id');
    }
}
