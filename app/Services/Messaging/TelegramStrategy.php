<?php

namespace App\Services\Messaging;

use App\DTO\MessageDTO;
use App\Models\TelegramRecipient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use App\Services\Interfaces\MessageStrategy;
use App\Jobs\SendMessageJob;
use Illuminate\Bus\PendingBatch;

class TelegramStrategy implements MessageStrategy
{
    protected string $apiUrl;

    public function __construct(protected string $token) {
        $this->apiUrl = "https://api.telegram.org/bot{$token}/";
    }

    public function sendMessage(string $to, string $message, ?string $attachment = null): bool
    {
        $response = Http::post($this->apiUrl . 'sendMessage', [
            'chat_id' => $to,
            'text' => $message,
        ]);
        $success = $response->successful();

        Log::info("[TELEGRAM] Sending to $to: $message | Success: " . ($success ? 'Yes' : 'No'));

        return $success;
    }

    public function sendMassMessage(MessageDTO $data): PendingBatch
    {
        // Find users with an associated chat id from polling
        $normalizedUsernames = array_map(fn($u) => ltrim($u, '@'), $data->recipients);
        $users = TelegramRecipient::whereIn('username', $normalizedUsernames)->pluck('chat_id', 'username');

        $jobs = [];
        foreach ($normalizedUsernames as $username) {
            $chatId = $users[$username] ?? null;

            if ($chatId) {
                $jobs = new SendMessageJob($this->platformName(), $username, $chatId, $data->message, $data->attachment, $data->userId);
            } else {
                Log::warning("[TELEGRAM] Chat ID not found for username: {$username}");
            }
        }

        return Bus::batch($jobs)->allowFailures();

        // Clear cache for first 10 pages
        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->finally(function () use ($data) {
                    $maxPagesToClear = 10;
                    for ($page = 1; $page <= $maxPagesToClear; $page++) {
                        Cache::forget("sent_messages_user_{$data->userId}_page_{$page}");
                    }
                })
                ->allowFailures()
                ->dispatch();
        }
    }

    public function platformName(): string
    {
        return 'telegram';
    }

    // For polling new users
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 30): ?array
    {
        $response = Http::get($this->apiUrl . 'getUpdates', [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['ok']) {
                return $data['result'];
            }
        }

        Log::error('Telegram getUpdates failed', ['response' => $response->body()]);

        return null;
    }
}