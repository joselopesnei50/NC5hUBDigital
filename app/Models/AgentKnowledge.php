<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentKnowledge extends Model
{
    use HasFactory;

    protected $table = 'agent_knowledge';

    protected $guarded = [];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
