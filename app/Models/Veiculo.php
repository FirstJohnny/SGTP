<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Veiculo extends Model
{
    use SoftDeletes;

    protected $table = 'veiculo';

    protected $fillable = [
        'placa',
        'chassi',
        'marca',
        'modelo',
        'ano_fabricado',
        'cor',
        'lotacao',
        'tipo_combustivel',
        'consumo_medio',
        'km_atual',
        'data_aquisicao',
        'status',
        'ultima_inspecao',
        'proxima_inspecao',
        'seguro_validade',
        'observacoes'
    ];

    protected $casts = [
        'ano_fabricado' => 'integer',
        'lotacao' => 'integer',
        'consumo_medio' => 'float',
        'km_atual' => 'integer',
        'data_aquisicao' => 'date',
        'ultima_inspecao' => 'date',
        'proxima_inspecao' => 'date',
        'seguro_validade' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes para status
    const STATUS_ATIVO = 'ATIVO';
    const STATUS_MANUTENCAO = 'MANUTENCAO';
    const STATUS_INATIVO = 'INATIVO';

    // Constantes para tipo combustível
    const COMBUSTIVEL_DIESEL = 'DIESEL';
    const COMBUSTIVEL_GASOLINA = 'GASOLINA';
    const COMBUSTIVEL_ELETRICO = 'ELETRICO';
    const COMBUSTIVEL_HIBRIDO = 'HIBRIDO';

    /**
     * Relacionamento com escalas
     */
    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class, 'veiculo_id');
    }

    /**
     * Relacionamento com manutenções
     */
    public function manutencoes(): HasMany
    {
        return $this->hasMany(Manutencao::class, 'veiculo_id');
    }

    /**
     * Relacionamento com abastecimentos
     */
    public function abastecimentos(): HasMany
    {
        return $this->hasMany(Abastecimento::class, 'veiculo_id');
    }

    /**
     * Relacionamento com documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoVeiculo::class, 'veiculo_id');
    }

    /**
     * Relacionamento com rastreamento GPS
     */
    public function rastreamentos(): HasMany
    {
        return $this->hasMany(RastreamentoGps::class, 'veiculo_id');
    }

    /**
     * Relacionamento com ocorrências
     */
    public function ocorrencias(): HasMany
    {
        return $this->hasMany(Ocorrencia::class, 'veiculo_id');
    }

    /**
     * Último rastreamento GPS
     */
    public function ultimoRastreamento()
    {
        return $this->hasOne(RastreamentoGps::class, 'veiculo_id')->latest('timestamp');
    }

    /**
     * Verificar se veículo está disponível para operação
     */
    public function isDisponivel(): bool
    {
        return $this->status === self::STATUS_ATIVO
            && $this->seguro_validade >= now()
            && (!$this->proxima_inspecao || $this->proxima_inspecao >= now());
    }

    /**
     * Verificar se documentação está em dia
     */
    public function documentacaoOk(): array
    {
        $status = [
            'seguro' => $this->seguro_validade >= now(),
            'inspecao' => !$this->proxima_inspecao || $this->proxima_inspecao >= now(),
            'valido' => true
        ];

        $status['valido'] = $status['seguro'] && $status['inspecao'];

        return $status;
    }

    /**
     * Scope para veículos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    /**
     * Scope para veículos em manutenção
     */
    public function scopeEmManutencao($query)
    {
        return $query->where('status', self::STATUS_MANUTENCAO);
    }

    /**
     * Scope para veículos com seguro vencendo (próximos 30 dias)
     */
    public function scopeSeguroVencendo($query)
    {
        return $query->where('seguro_validade', '<=', now()->addDays(30))
                     ->where('seguro_validade', '>=', now());
    }
}
