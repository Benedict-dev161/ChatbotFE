<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $table = 'conversations';
    protected $primaryKey = 'conversation_id';

    public $timestamps = true;

    public const CREATED_AT = 'started_at';
    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'customer_id',
        'assigned_cs_id',
        'current_status',
        'started_at',
        'ended_at',
        'updated_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class, 'customer_id', 'user_id');
    }

    public function assignedCs(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class, 'assigned_cs_id', 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id', 'conversation_id')
            ->orderBy('created_at');
    }

    public function state(): HasOne
    {
        return $this->hasOne(ConversationState::class, 'conversation_id', 'conversation_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'conversation_id', 'conversation_id');
    }

    public function customerForms(): HasMany
    {
        return $this->hasMany(CustomerForm::class, 'conversation_id', 'conversation_id');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class, 'conversation_id', 'conversation_id');
    }
}
