<?php

declare(strict_types=1);

namespace App\Agent\Enums;

enum AgentInsightCategory: string
{
    case PEDIDOS = 'pedidos';
    case CAIXA = 'caixa';
    case CLIENTES = 'clientes';
    case PRODUTOS = 'produtos';
}
