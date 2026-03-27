<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissao extends Model
{
    use SoftDeletes;

    protected $table = 'permissao';

    protected $fillable = [
        'nome',
        'descricao',
        'modulo'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relacionamento com perfis de acesso (N:N)
     */
    public function perfis(): BelongsToMany
    {
        return $this->belongsToMany(PerfilAcesso::class, 'perfil_acesso_permissao', 'permissao_id', 'perfil_id')
                    ->withTimestamps();
    }
}
