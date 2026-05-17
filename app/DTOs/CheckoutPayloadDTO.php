<?php

namespace App\DTOs;

class CheckoutPayloadDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly array $roomItems,
        public readonly array $boatItems
    ) {}

    public static function fromRequestAndCart(array $validatedRequest, array $cart): self
    {
        return new self(
            name: $validatedRequest['name'],
            email: $validatedRequest['email'],
            phone: $validatedRequest['phone'],
            roomItems: array_values(array_filter($cart, fn($i) => isset($i['room_id']))),
            boatItems: array_values(array_filter($cart, fn($i) => isset($i['boat_id'])))
        );
    }

    public function isEmpty(): bool
    {
        return empty($this->roomItems) && empty($this->boatItems);
    }
}
