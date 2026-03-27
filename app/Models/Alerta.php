<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $table = 'alerta';

    protected $fillable = [
        'veiculo_id',
        'tipo',
        'gravidade',
        'mensagem',
        'latitude',
        'longitude',
        'timestamp',
        'resolvido',
        'resolvido_por',
        'data_resolucao'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'timestamp' => 'datetime',
        'resolvido' => 'boolean',
        'data_resolucao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_MANUTENCAO = 'MANUTENCAO';
    const TIPO_SEGURANCA = 'SEGURANCA';
    const TIPO_GPS = 'GPS';
    const TIPO_DOCUMENTO = 'DOCUMENTO';
    const TIPO_OUTRO = 'OUTRO';

    // Constantes para gravidade
    const GRAVIDADE_LEVE = 'LEVE';
    const GRAVIDADE_MEDIA = 'MEDIA';
    const GRAVIDADE_GRAVE = 'GRAVE';


    /**
     * Relacionamento com veículo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Relacionamento com resolvedor
     */
    public function resolvedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolvido_por');
    }

    /**
     * Resolver alerta
     */
    public function resolver(int $usuarioId)
    {
        $this->resolvido = true;
        $this->resolvido_por = $usuarioId;
        $this->data_resolucao = now();
        $this->save();
    }
}
