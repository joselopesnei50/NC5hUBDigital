<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tool_calls' => 'array',
        'cost' => 'decimal:4',
    ];

    public function conversation()
    {
        return $this->belongsTo(AgentConversation::class, 'conversation_id');
    }
}
