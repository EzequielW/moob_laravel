<?php

namespace App\Console\Commands;

use App\Models\TelegramRecipient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Messaging\TelegramStrategy;

class TelegramPollUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:poll-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll Telegram updates via getUpdates';

    protected TelegramStrategy $telegram;

    public function __construct(TelegramStrategy $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    public function handle()
    {
        $lastUpdateId = cache('telegram_last_update_id', 0);

        $updates = $this->telegram->getUpdates($lastUpdateId + 1);
        if (is_null($updates)) {
            $this->error('Failed to fetch updates.');
            return 1;
        }

        foreach ($updates as $update) {
            $this->processUpdate($update);
            $lastUpdateId = max($lastUpdateId, $update['update_id']);
        }

        cache(['telegram_last_update_id' => $lastUpdateId], now()->addDay());

        $this->info('Processed ' . count($updates) . ' updates.');

        return 0;
    }

    protected function processUpdate(array $update)
    {
        if (!isset($update['message'])) {
            return;
        }

        $message = $update['message'];
        $chat = $message['chat'] ?? null;
        if (!$chat) {
            return;
        }

        $chatId = $chat['id'];
        $username = $chat['username'] ?? null;

        TelegramRecipient::updateOrCreate(
            ['chat_id' => $chatId],
            ['username' => $username]
        );

        $text = $message['text'] ?? '[non-text message]';
        Log::info("Received message from Telegram user {$chatId} ({$username}): {$text}");
    }
}
