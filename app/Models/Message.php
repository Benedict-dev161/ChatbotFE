<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use SoftDeletes;

    protected $table = 'messages';
    protected $primaryKey = 'message_id';

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'intent_id',
        'content',
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class, 'sender_id', 'user_id');
    }

    public function intent(): BelongsTo
    {
        return $this->belongsTo(Intent::class, 'intent_id', 'intent_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class, 'message_id', 'message_id');
    }
}
