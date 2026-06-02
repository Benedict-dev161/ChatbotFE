<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Escalation extends Model
{
    protected $table = 'escalations';
    protected $primaryKey = 'escalation_id';

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'from_ai',
        'assigned_cs_id',
        'reason',
        'status',
        'created_at',
    ];

    protected $casts = [
        'from_ai' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }

    public function assignedCs(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class, 'assigned_cs_id', 'user_id');
    }
}
