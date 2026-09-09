<?php

declare(strict_types=1);

namespace App\Models;

use App\Agent\Enums\AgentInsightCategory;
use App\Agent\Enums\AgentInsightSeverity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentInsight extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'evidence' => 'array',
        'category' => AgentInsightCategory::class,
        'severity' => AgentInsightSeverity::class,
    ];

    public function run()
    {
        return $this->belongsTo(AgentRun::class, 'agent_run_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
