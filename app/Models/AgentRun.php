<?php

declare(strict_types=1);

namespace App\Models;

use App\Agent\Enums\AgentRunStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentRun extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'snapshot' => 'array',
        'status' => AgentRunStatus::class,
        'cost' => 'decimal:4',
        'duration_seconds' => 'float',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function insights()
    {
        return $this->hasMany(AgentInsight::class);
    }
}
