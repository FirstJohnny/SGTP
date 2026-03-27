<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PontoVenda extends Model
{
    protected $table = 'ponto_venda';

    protected $fillable = [
        'nome',
        'latitude',
        'longitude',
        'endereco',
        'operador_responsavel',
        'stock_bilhetes',
        'ativo'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'stock_bilhetes' => 'integer',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com operador responsável
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_responsavel');
    }

    /**
     * Relacionamento com bilhetes vendidos
     */
    public function bilhetes(): HasMany
    {
        return $this->hasMany(Bilhete::class, 'ponto_venda_id');
    }

    /**
     * Reduzir stock de bilhetes
     */
    public function reduzirStock(int $quantidade): bool
    {
        if ($this->stock_bilhetes >= $quantidade) {
            $this->stock_bilhetes -= $quantidade;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Aumentar stock de bilhetes
     */
    public function aumentarStock(int $quantidade)
    {
        $this->stock_bilhetes += $quantidade;
        $this->save();
    }
}
