<?php

declare(strict_types=1);

namespace App\Agent\DTOs;

readonly class LlmResponse
{
    public function __construct(
        public string $content,
        public int $tokensIn,
        public int $tokensOut,
        public float $estimatedCost,
        public ?array $toolCalls = null
    ) {
    }
}
