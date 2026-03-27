<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manutencao extends Model
{
    protected $table = 'manutencao';

    protected $fillable = [
        'veiculo_id',
        'ocorrencia_id',
        'tipo',
        'descricao',
        'data_agendamento',
        'data_inicio',
        'data_fim',
        'oficina',
        'custo_pecas',
        'custo_mao_obra',
        'custo_total',
        'observacoes',
        'status'
    ];

    protected $casts = [
        'data_agendamento' => 'date',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'custo_pecas' => 'decimal:2',
        'custo_mao_obra' => 'decimal:2',
        'custo_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_PREVENTIVA = 'PREVENTIVA';
    const TIPO_CORRETIVA = 'CORRETIVA';
    const TIPO_EMERGENCIAL = 'EMERGENCIAL';

    // Constantes para status
    const STATUS_AGENDADA = 'AGENDADA';
    const STATUS_EM_EXECUCAO = 'EM_EXECUCAO';
    const STATUS_CONCLUIDA = 'CONCLUIDA';
    const STATUS_CANCELADA = 'CANCELADA';

    /**
     * Relacionamento com veículo
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    /**
     * Relacionamento com ocorrência
     */
    public function ocorrencia(): BelongsTo
    {
        return $this->belongsTo(Ocorrencia::class, 'ocorrencia_id');
    }

    /**
     * Relacionamento com peças trocadas
     */
    public function pecasTrocadas(): HasMany
    {
        return $this->hasMany(PecaTrocada::class, 'manutencao_id');
    }

    /**
     * Iniciar manutenção
     */
    public function iniciar()
    {
        $this->status = self::STATUS_EM_EXECUCAO;
        $this->data_inicio = now();
        $this->save();
    }

    /**
     * Concluir manutenção
     */
    public function concluir()
    {
        $this->status = self::STATUS_CONCLUIDA;
        $this->data_fim = now();

        // Atualizar status do veículo
        $this->veiculo->status = Veiculo::STATUS_ATIVO;
        $this->veiculo->ultima_inspecao = now();
        $this->veiculo->save();

        $this->save();
    }

    /**
     * Calcular custo total
     */
    public function calcularCustoTotal(): float
    {
        $this->custo_total = $this->custo_pecas + $this->custo_mao_obra;
        return $this->custo_total;
    }
}
