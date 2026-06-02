<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intent extends Model
{
    protected $table = 'intents';
    protected $primaryKey = 'intent_id';

    public $timestamps = false;

    protected $fillable = [
        'intent_name',
        'description',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'intent_id', 'intent_id');
    }
}
