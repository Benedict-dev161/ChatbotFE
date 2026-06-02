<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    protected $table = 'system_logs';
    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'conversation_id',
        'event_type',
        'message',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class, 'user_id', 'user_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }
}
