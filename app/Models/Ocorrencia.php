<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ocorrencia extends Model
{
    protected $table = 'ocorrencia';

    protected $fillable = [
        'veiculo_id',
        'escala_id',
        'colaborador_id',
        'tipo',
        'gravidade',
        'descricao',
        'latitude',
        'longitude',
        'data_ocorrencia',
        'fotos_url',
        'status',
        'sincronizado',
        'data_sincronizacao'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'data_ocorrencia' => 'datetime',
        'fotos_url' => 'array',
        'sincronizado' => 'boolean',
        'data_sincronizacao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_ACIDENTE = 'ACIDENTE';
    const TIPO_ATRASO = 'ATRASO';
    const TIPO_FALHA_MECANICA = 'FALHA_MECANICA';
    const TIPO_ASSALTO = 'ASSALTO';
    const TIPO_OUTRO = 'OUTRO';

    // Constantes para gravidade
    const GRAVIDADE_LEVE = 'LEVE';
    const GRAVIDADE_MEDIA = 'MEDIA';
    const GRAVIDADE_GRAVE = 'GRAVE';
    const GRAVIDADE_CRITICA = 'CRITICA';

    // Constantes para status
    const STATUS_ABERTA = 'ABERTA';
    const STATUS_EM_ANALISE = 'EM_ANALISE';
    const STATUS_RESOLVIDA = 'RESOLVIDA';
    const STATUS_CANCELADA = 'CANCELADA';

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
     * Relacionamento com colaborador
     */
    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }

    /**
     * Relacionamento com manutenção (se houver)
     */
    public function manutencao()
    {
        return $this->hasOne(Manutencao::class, 'ocorrencia_id');
    }

    /**
     * Resolver ocorrência
     */
    public function resolver()
    {
        $this->status = self::STATUS_RESOLVIDA;
        $this->save();
    }
}
