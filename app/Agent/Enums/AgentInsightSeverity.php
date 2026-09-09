<?php

declare(strict_types=1);

namespace App\Agent\Enums;

enum AgentInsightSeverity: string
{
    case INFO = 'info';
    case ATENCAO = 'atencao';
    case CRITICO = 'critico';
}
