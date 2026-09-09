<?php

declare(strict_types=1);

namespace App\Agent\Enums;

enum AgentRunStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case DEGRADED = 'degraded'; // Falhou a geração de texto, mas entregou os números
}
