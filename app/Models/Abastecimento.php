<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abastecimento extends Model
{
    protected $table = 'abastecimento';

    protected $fillable = [
        'veiculo_id',
        'motorista_id',
        'data',
        'odometro',
        'litros',
        'valor_total',
        'preco_litro',
        'posto',
        'tipo_combustivel',
        'comprovativo_url'
    ];

    protected $casts = [
        'data' => 'date',
        'odometro' => 'integer',
        'litros' => 'float',
        'valor_total' => 'decimal:2',
        'preco_litro' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com veículo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Relacionamento com motorista
     */
    public function motorista(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'motorista_id');
    }

    /**
     * Calcular consumo médio após abastecimento
     */
    public function calcularConsumoMedio(): ?float
    {
        $ultimoAbastecimento = Abastecimento::where('veiculo_id', $this->veiculo_id)
            ->where('id', '<', $this->id)
            ->orderBy('data', 'desc')
            ->first();

        if ($ultimoAbastecimento) {
            $kmRodados = $this->odometro - $ultimoAbastecimento->odometro;
            if ($kmRodados > 0 && $this->litros > 0) {
                return $kmRodados / $this->litros;
            }
        }

        return null;
    }
}
