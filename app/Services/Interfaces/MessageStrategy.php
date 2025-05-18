<?php

namespace App\Services\Interfaces;

use App\DTO\MessageDTO;
use Illuminate\Bus\PendingBatch;

interface MessageStrategy
{
    public function sendMessage(string $to, string $message, ?string $attachment = null): bool;
    public function sendMassMessage(MessageDTO $data): PendingBatch;
    public function platformName(): string;
}