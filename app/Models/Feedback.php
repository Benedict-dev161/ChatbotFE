<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';
    protected $primaryKey = 'feedback_id';

    public $timestamps = false;

    protected $fillable = [
        'message_id',
        'rating',
        'comment',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id', 'message_id');
    }
}
