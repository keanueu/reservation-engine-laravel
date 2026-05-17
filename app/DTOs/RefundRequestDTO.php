<?php

namespace App\DTOs;

class RefundRequestDTO
{
    public function __construct(
        public readonly float|null $amount,
        public readonly string|null $reason
    ) {}

    public static function fromRequest(array $validatedRequest): self
    {
        return new self(
            amount: isset($validatedRequest['amount']) ? (float) $validatedRequest['amount'] : null,
            reason: $validatedRequest['reason'] ?? null
        );
    }
}
