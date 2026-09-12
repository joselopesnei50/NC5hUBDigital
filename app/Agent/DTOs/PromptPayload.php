<?php

declare(strict_types=1);

namespace App\Agent\DTOs;

class PromptPayload
{
    public function __construct(
        public string $systemPrompt,
        public string $userPrompt,
        public ?array $snapshot = null,
        public ?array $tools = null,
        public ?array $history = null,
        // "auto" (padrao), "none" (proibe chamar tools) ou "required" (obriga)
        public ?string $toolChoice = null
    ) {
    }
}
