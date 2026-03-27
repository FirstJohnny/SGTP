<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeNaoLidas($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }
}