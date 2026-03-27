<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAuditoria extends Model
{
    protected $table = 'log_auditoria';

    protected $fillable = [
        'usuario_id',
        'acao',
        'entidade',
        'entidade_id',
        'dados_anteriores',
        'dados_novos',
        'ip_address',
        'user_agent',
        'timestamp'
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com usuário
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Registrar log de ação
     */
    public static function registrar($usuarioId, $acao, $entidade, $entidadeId, $dadosAntigos = null, $dadosNovos = null)
    {
        return self::create([
            'usuario_id' => $usuarioId,
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId,
            'dados_anteriores' => $dadosAntigos,
            'dados_novos' => $dadosNovos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }

    /**
     * Scope para logs por entidade
     */
    public function scopePorEntidade($query, string $entidade, $entidadeId = null)
    {
        $query->where('entidade', $entidade);

        if ($entidadeId) {
            $query->where('entidade_id', $entidadeId);
        }

        return $query;
    }
}
