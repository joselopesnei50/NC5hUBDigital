<?php

declare(strict_types=1);

namespace App\Agent\Exceptions;

use Exception;

class LlmException extends Exception
{
    // Exceção customizada para isolar erros de timeout/API do resto da aplicação
}
