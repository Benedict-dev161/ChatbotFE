<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatUser extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password_hash',
        'role',
        'status',
        'availability_status',
        'created_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'customer_id', 'user_id');
    }

    public function assignedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'assigned_cs_id', 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }
}
