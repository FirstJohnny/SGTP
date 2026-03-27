<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';
    
    protected $fillable = [
        'nome',
        'email',
        'tipo',
        'mensagem',
        'rota_id',
        'veiculo_id',
        'ip_address',
        'data_envio',
        'lido',
        'resposta',
        'respondido_em'
    ];

    protected $casts = [
        'data_envio' => 'datetime',
        'respondido_em' => 'datetime',
        'lido' => 'boolean'
    ];

    const TIPO_ELOGIO = 'ELOGIO';
    const TIPO_SUGESTAO = 'SUGESTAO';
    const TIPO_RECLAMACAO = 'RECLAMACAO';
    const TIPO_DUVIDA = 'DUVIDA';

    public function rota(): BelongsTo
    {
        return $this->belongsTo(Rota::class, 'rota_id');
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function scopeNaoLidos($query)
    {
        return $query->where('lido', false);
    }

    public function marcarComoLido()
    {
        $this->lido = true;
        $this->save();
    }

    public function responder(string $resposta)
    {
        $this->resposta = $resposta;
        $this->respondido_em = now();
        $this->save();
    }
}