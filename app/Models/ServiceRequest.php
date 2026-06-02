<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';
    protected $primaryKey = 'request_id';

    public $timestamps = true;

    protected $fillable = [
        'conversation_id',
        'customer_id',
        'service_id',
        'request_status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'conversation_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class, 'customer_id', 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}
