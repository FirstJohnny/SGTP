<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    protected $table = 'despesa';

    protected $fillable = [
        'veiculo_id',
        'tipo',
        'valor',
        'data',
        'descricao',
        'documento_url',
        'aprovado_por',
        'aprovado'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data' => 'date',
        'aprovado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_MANUTENCAO = 'MANUTENCAO';
    const TIPO_COMBUSTIVEL = 'COMBUSTIVEL';
    const TIPO_SEGURO = 'SEGURO';
    const TIPO_MULTA = 'MULTA';
    const TIPO_SALARIO = 'SALARIO';
    const TIPO_OUTRO = 'OUTRO';

    /**
     * Relacionamento com veículo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Relacionamento com aprovador
     */
    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    /**
     * Aprovar despesa
     */
    public function aprovar(int $usuarioId)
    {
        $this->aprovado = true;
        $this->aprovado_por = $usuarioId;
        $this->save();
    }
}
