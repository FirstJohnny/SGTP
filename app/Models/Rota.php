<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $nome
 * @property string $codigo
 * @property string|null $descricao
 * @property string $tipo
 * @property float $distancia_total
 * @property int|null $tempo_estimado
 * @property array|null $trajeto_geojson
 * @property bool $ativa
 * @property string|null $empresa_responsavel
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Rota extends Model
{
    use SoftDeletes;

    protected $table = 'rota';
    
    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'tipo',
        'distancia_total',
        'tempo_estimado',
        'trajeto_geojson',
        'ativa',
        'empresa_responsavel'
    ];

    protected $casts = [
        'distancia_total' => 'float',
        'tempo_estimado' => 'integer',
        'trajeto_geojson' => 'array',
        'ativa' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_URBANA = 'URBANA';
    const TIPO_INTERMUNICIPAL = 'INTERMUNICIPAL';
    const TIPO_RODOVIARIA = 'RODOVIARIA';
    const TIPO_ESCOLAR = 'ESCOLAR';

    /**
     * Relacionamento com pontos de paragem (via ponto_rota)
     */
    public function pontosParagem(): BelongsToMany
    {
        return $this->belongsToMany(PontoParagem::class, 'ponto_rota', 'rota_id', 'ponto_paragem_id')
                    ->withPivot('ordem', 'tempo_estimado_chegada', 'distancia_desde_inicio')
                    ->orderBy('pivot_ordem');
    }

    /**
     * Relacionamento com ponto_rota (intermediária)
     */
    public function pontosRota(): HasMany
    {
        return $this->hasMany(PontoRota::class, 'rota_id');
    }

    /**
     * Relacionamento com horários
     */
    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'rota_id');
    }

    /**
     * Relacionamento com tarifas
     */
    public function tarifas(): HasMany
    {
        return $this->hasMany(Tarifa::class, 'rota_id');
    }

    /**
     * Relacionamento com escalas
     */
    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class, 'rota_id');
    }

    /**
     * Obter tarifa atual para um tipo de passageiro
     */
    public function tarifaAtual(string $tipoPassageiro)
    {
        return $this->tarifas()
            ->where('tipo_passageiro', $tipoPassageiro)
            ->where('ativa', true)
            ->where('data_inicio', '<=', now())
            ->where(function($q) {
                $q->whereNull('data_fim')->orWhere('data_fim', '>=', now());
            })
            ->first();
    }

    /**
     * Verificar se rota está ativa
     */
    public function isAtiva(): bool
    {
        return $this->ativa;
    }

    /**
     * Scope para rotas ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }

    /**
     * Scope por tipo
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
