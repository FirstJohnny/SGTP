<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PecaTrocada extends Model
{
    protected $table = 'peca_trocada';

    protected $fillable = [
        'manutencao_id',
        'nome_peca',
        'quantidade',
        'preco_unitario',
        'garantia_meses'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'garantia_meses' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com manutenção
     */
    public function manutencao(): BelongsTo
    {
        return $this->belongsTo(Manutencao::class, 'manutencao_id');
    }

    /**
     * Calcular subtotal
     */
    public function subtotal(): float
    {
        return $this->quantidade * $this->preco_unitario;
    }
}
