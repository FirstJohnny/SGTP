<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'bi',
        'telefone',
        'tipo_usuario',
        'status',
        'ultimo_acesso',
        'perfil_acesso_id',
        'two_factor_enabled',
        'two_factor_secret'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acesso' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constantes para tipo usuário
    const TIPO_ADMIN = 'ADMIN';
    const TIPO_GESTOR_OPERACOES = 'GESTOR_OPERACOES';
    const TIPO_GESTOR_FROTA = 'GESTOR_FROTA';
    const TIPO_FISCAL = 'FISCAL';
    const TIPO_OPERADOR_BILHETICA = 'OPERADOR_BILHETICA';
    const TIPO_FINANCEIRO = 'FINANCEIRO';

    // Constantes para status
    const STATUS_ATIVO = 'ATIVO';
    const STATUS_INATIVO = 'INATIVO';
    const STATUS_BLOQUEADO = 'BLOQUEADO';

    /**
     * Relacionamento com perfil de acesso
     */
    public function perfilAcesso(): BelongsTo
    {
        return $this->belongsTo(PerfilAcesso::class, 'perfil_acesso_id');
    }

    /**
     * Relacionamento com colaborador
     */
    public function colaborador()
    {
        return $this->hasOne(Colaborador::class, 'user_id');
    }

    /**
     * Relacionamento com logs de auditoria
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LogAuditoria::class, 'usuario_id');
    }

    /**
     * Relacionamento com vendas de bilhetes
     */
    public function bilhetesVendidos(): HasMany
    {
        return $this->hasMany(Bilhete::class, 'operador_id');
    }

    /**
     * Relacionamento com fechamentos de caixa
     */
    public function fechamentosCaixa(): HasMany
    {
        return $this->hasMany(FechoCaixa::class, 'operador_id');
    }

    /**
     * Verificar se usuário é administrador
     */
    public function isAdmin(): bool
    {
        return $this->tipo_usuario === self::TIPO_ADMIN;
    }

    /**
     * Verificar se usuário tem permissão
     */
    public function hasPermissao(string $permissaoNome): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->perfilAcesso && $this->perfilAcesso->hasPermissao($permissaoNome);
    }

    /**
     * Verificar se usuário está ativo
     */
    public function isAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    /**
     * Registrar último acesso
     */
    public function registrarAcesso()
    {
        $this->ultimo_acesso = now();
        $this->save();

        LogAuditoria::registrar(
            $this->id,
            'LOGIN',
            'users',
            $this->id,
            null,
            ['ip' => request()->ip()]
        );
    }
}
