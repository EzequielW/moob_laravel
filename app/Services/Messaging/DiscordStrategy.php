<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use App\Services\Interfaces\MessageStrategy;
use App\Jobs\SendMessageJob;
use App\DTO\MessageDTO;

class DiscordStrategy implements MessageStrategy
{
    public function sendMessage(string $to, string $message, ?string $attachment = null): bool
    {
        Log::info("[DISCORD] Sending to $to: $message");
        return true;
    }

    public function sendMassMessage(MessageDTO $data): PendingBatch
    {
        $jobs = [];
        foreach ($data->recipients as $recipient) {
            $jobs[] = new SendMessageJob($this->platformName(), $recipient, $recipient, $data->message, $data->attachment, $data->userId);
        }

        return Bus::batch($jobs)->allowFailures();
    }
    
    public function platformName(): string
    {
        return 'discord';
    }
}