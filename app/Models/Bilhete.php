<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bilhete extends Model
{
    protected $table = 'bilhete';

    protected $fillable = [
        'codigo_barras',
        'tarifa_id',
        'valor_pago',
        'data_venda',
        'ponto_venda_id',
        'operador_id',
        'status',
        'data_validade',
        'forma_pagamento'
    ];

    protected $casts = [
        'valor_pago' => 'decimal:2',
        'data_venda' => 'datetime',
        'data_validade' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para status
    const STATUS_VALIDO = 'VALIDO';
    const STATUS_UTILIZADO = 'UTILIZADO';
    const STATUS_CANCELADO = 'CANCELADO';
    const STATUS_EXPIRADO = 'EXPIRADO';

    // Constantes para forma pagamento
    const PAGAMENTO_DINHEIRO = 'DINHEIRO';
    const PAGAMENTO_CARTAO = 'CARTAO';
    const PAGAMENTO_PIX = 'PIX';
    const PAGAMENTO_TRANSFERENCIA = 'TRANSFERENCIA';
    const PAGAMENTO_OUTRO = 'OUTRO';

    /**
     * Relacionamento com tarifa
     */
    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class, 'tarifa_id');
    }

    /**
     * Relacionamento com ponto de venda
     */
    public function pontoVenda(): BelongsTo
    {
        return $this->belongsTo(PontoVenda::class, 'ponto_venda_id');
    }

    /**
     * Relacionamento com operador (usuário)
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    /**
     * Relacionamento com validação
     */
    public function validacao(): HasOne
    {
        return $this->hasOne(ValidacaoBilhete::class, 'bilhete_id');
    }

    /**
     * Verificar se bilhete é válido
     */
    public function isValid(): bool
    {
        return $this->status === self::STATUS_VALIDO && $this->data_validade >= now();
    }

    /**
     * Validar bilhete (usar)
     */
    public function validar(array $dadosValidacao): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->status = self::STATUS_UTILIZADO;
        $this->save();

        return ValidacaoBilhete::create([
            'bilhete_id' => $this->id,
            'veiculo_id' => $dadosValidacao['veiculo_id'],
            'escala_id' => $dadosValidacao['escala_id'],
            'latitude' => $dadosValidacao['latitude'] ?? null,
            'longitude' => $dadosValidacao['longitude'] ?? null,
            'timestamp' => now(),
            'metodo' => $dadosValidacao['metodo']
        ])->exists();
    }
}
