<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBase extends Model
{
    protected $table = 'knowledge_base';
    protected $primaryKey = 'kb_id';

    public $timestamps = false;

    protected $fillable = [
        'intent_id',
        'category',
        'question_pattern',
        'answer',
        'keywords',
        'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function intent(): BelongsTo
    {
        return $this->belongsTo(Intent::class, 'intent_id', 'intent_id');
    }
}
