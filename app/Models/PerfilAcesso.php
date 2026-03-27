<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PerfilAcesso extends Model
{
    use SoftDeletes;

    protected $table = 'perfil_acesso';

    protected $fillable = [
        'nome',
        'descricao'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relacionamento com permissões (N:N)
     */
    public function permissoes(): BelongsToMany
    {
        return $this->belongsToMany(Permissao::class, 'perfil_acesso_permissao', 'perfil_id', 'permissao_id')
                    ->withTimestamps();
    }

    /**
     * Relacionamento com usuários
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'perfil_acesso_id');
    }

    /**
     * Verificar se o perfil tem uma permissão específica
     */
    public function hasPermissao(string $permissaoNome): bool
    {
        return $this->permissoes()->where('nome', $permissaoNome)->exists();
    }

    /**
     * Verificar se o perfil tem todas as permissões
     */
    public function hasAllPermissoes(array $permissoesNomes): bool
    {
        $permissoes = $this->permissoes()->whereIn('nome', $permissoesNomes)->count();
        return $permissoes === count($permissoesNomes);
    }
}
