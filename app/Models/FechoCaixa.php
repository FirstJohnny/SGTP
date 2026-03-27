<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FechoCaixa extends Model
{
    protected $table = 'fecho_caixa';

    protected $fillable = [
        'operador_id',
        'data_fecho',
        'valor_esperado',
        'valor_apurado',
        'diferenca',
        'observacoes',
        'status'
    ];

    protected $casts = [
        'data_fecho' => 'datetime',
        'valor_esperado' => 'decimal:2',
        'valor_apurado' => 'decimal:2',
        'diferenca' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para status
    const STATUS_ABERTO = 'ABERTO';
    const STATUS_FECHADO = 'FECHADO';
    const STATUS_CONFERIDO = 'CONFERIDO';

    /**
     * Relacionamento com operador
     */
    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    /**
     * Calcular diferença
     */
    public function calcularDiferenca(): float
    {
        $this->diferenca = $this->valor_apurado - $this->valor_esperado;
        return $this->diferenca;
    }

    /**
     * Fechar caixa
     */
    public function fechar(float $valorApurado)
    {
        $this->valor_apurado = $valorApurado;
        $this->calcularDiferenca();
        $this->status = self::STATUS_FECHADO;
        $this->save();
    }
}
