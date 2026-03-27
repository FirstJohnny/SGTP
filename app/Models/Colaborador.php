<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Colaborador extends Model
{
    use SoftDeletes;

    protected $table = 'colaborador';

    protected $fillable = [
        'tipo',
        'nome_completo',
        'bi',
        'numero_carta',
        'carta_validade',
        'categoria_carta',
        'data_contratacao',
        'data_demissao',
        'salario_base',
        'numero_seguranca_social',
        'telefone',
        'email',
        'foto_url',
        'emergencia_contato',
        'user_id'
    ];

    protected $casts = [
        'data_contratacao' => 'date',
        'data_demissao' => 'date',
        'carta_validade' => 'date',
        'salario_base' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes para tipos
    const TIPO_MOTORISTA = 'MOTORISTA';
    const TIPO_COBRADOR = 'COBRADOR';
    const TIPO_FISCAL = 'FISCAL';
    const TIPO_OUTRO = 'OUTRO';

    /**
     * Relacionamento com usuário do sistema
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento com escalas como motorista
     */
    public function escalasMotorista(): HasMany
    {
        return $this->hasMany(Escala::class, 'motorista_id');
    }

    /**
     * Relacionamento com escalas como cobrador
     */
    public function escalasCobrador(): HasMany
    {
        return $this->hasMany(Escala::class, 'cobrador_id');
    }

    /**
     * Relacionamento com ocorrências
     */
    public function ocorrencias(): HasMany
    {
        return $this->hasMany(Ocorrencia::class, 'colaborador_id');
    }

    /**
     * Relacionamento com abastecimentos
     */
    public function abastecimentos(): HasMany
    {
        return $this->hasMany(Abastecimento::class, 'motorista_id');
    }

    /**
     * Verificar se colaborador está ativo
     */
    public function isAtivo(): bool
    {
        return !$this->data_demissao || $this->data_demissao > now();
    }

    /**
     * Verificar se documento (carta) está válido
     */
    public function cartaValida(): bool
    {
        if ($this->tipo !== self::TIPO_MOTORISTA) {
            return true;
        }

        return $this->carta_validade && $this->carta_validade >= now();
    }

    /**
     * Obter escalas atuais (hoje)
     */
    public function escalasHoje()
    {
        return Escala::where(function($query) {
                $query->where('motorista_id', $this->id)
                      ->orWhere('cobrador_id', $this->id);
            })
            ->where('data', today())
            ->get();
    }

    /**
     * Scope para motoristas ativos
     */
    public function scopeMotoristas($query)
    {
        return $query->where('tipo', self::TIPO_MOTORISTA)
                     ->whereNull('data_demissao');
    }

    /**
     * Scope para colaboradores ativos
     */
    public function scopeAtivos($query)
    {
        return $query->whereNull('data_demissao');
    }
}
