<?php

declare(strict_types=1);

namespace App\Agent\Enums;

enum AgentNotificationStatus: string
{
    case RASCUNHO = 'rascunho';
    case APROVADO = 'aprovado';
    case ENVIADO = 'enviado';
    case DESCARTADO = 'descartado';
}
