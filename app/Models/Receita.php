<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receita extends Model
{
    protected $table = 'receita';

    protected $fillable = [
        'data',
        'valor_total',
        'origem',
        'descricao',
        'consolidado'
    ];

    protected $casts = [
        'data' => 'date',
        'valor_total' => 'decimal:2',
        'consolidado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para origem
    const ORIGEM_BILHETE = 'BILHETE';
    const ORIGEM_SUBSIDIO = 'SUBSIDIO';
    const ORIGEM_CONTRATO = 'CONTRATO';
    const ORIGEM_OUTROS = 'OUTROS';

    /**
     * Scope para receitas por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para receitas não consolidadas
     */
    public function scopeNaoConsolidadas($query)
    {
        return $query->where('consolidado', false);
    }
}
