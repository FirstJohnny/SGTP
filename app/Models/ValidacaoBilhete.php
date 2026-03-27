<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidacaoBilhete extends Model
{
    protected $table = 'validacao_bilhete';

    protected $fillable = [
        'bilhete_id',
        'veiculo_id',
        'escala_id',
        'latitude',
        'longitude',
        'timestamp',
        'metodo'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para método
    const METODO_QRCODE = 'QRCODE';
    const METODO_BARRAS = 'BARRAS';
    const METODO_NFC = 'NFC';
    const METODO_MANUAL = 'MANUAL';
    const METODO_OUTRO = 'OUTRO';

    /**
     * Relacionamento com bilhete
     */
    public function bilhete(): BelongsTo
    {
        return $this->belongsTo(Bilhete::class, 'bilhete_id');
    }

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
}
