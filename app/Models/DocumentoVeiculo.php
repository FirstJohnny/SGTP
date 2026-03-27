<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoVeiculo extends Model
{
    protected $table = 'documento_veiculo';

    protected $fillable = [
        'veiculo_id',
        'tipo',
        'numero_documento',
        'data_emissao',
        'data_validade',
        'arquivo_url',
        'observacoes'
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_validade' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_LICENCA = 'LICENCA';
    const TIPO_SEGURO = 'SEGURO';
    const TIPO_INSPECAO = 'INSPECAO';
    const TIPO_REGISTO = 'REGISTO';
    const TIPO_OUTRO = 'OUTRO';

    /**
     * Relacionamento com veículo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Verificar se documento está válido
     */
    public function isValid(): bool
    {
        return !$this->data_validade || $this->data_validade >= now();
    }

    /**
     * Verificar se está próximo do vencimento (30 dias)
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->data_validade) {
            return false;
        }

        return $this->data_validade <= now()->addDays(30) && $this->data_validade >= now();
    }
}
