<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerForm extends Model
{
    protected $table = 'customer_forms';
    protected $primaryKey = 'form_id';

    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'customer_id',
        'service_id',
        'project_name',
        'project_description',
        'budget',
        'deadline',
        'created_at',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'deadline' => 'date',
        'created_at' => 'datetime',
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
