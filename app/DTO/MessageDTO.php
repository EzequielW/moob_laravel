<?php

namespace App\DTO;

class MessageDTO
{
    public function __construct(
        public array $recipients, 
        public string $message, 
        public ?string $attachment, 
        public int $userId
    ) {}
}