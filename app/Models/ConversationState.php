<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationState extends Model
{
    protected $table = 'conversation_state';
    protected $primaryKey = 'state_id';

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'current_step',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }
}
