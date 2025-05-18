<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batchable;
use App\Factories\MessageStrategyFactory;
use App\Models\Message;

class SendMessageJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $platform,
        protected string $username,
        protected string $recipient, 
        protected string $message, 
        protected ?string $attachment, 
        protected int $userId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(MessageStrategyFactory $factory): void
    {
        $messageStrategy = $factory->make($this->platform);

        $success = $messageStrategy->sendMessage($this->recipient, $this->message, $this->attachment);
        if(!$success) 
        {
            $this->fail(new \Exception("Message failed for {$this->username}"));
        }
        else 
        {
            Message::create([
                'user_id' => $this->userId,
                'platform' => $this->platform,
                'recipient' => $this->username,
                'content' => $this->message,
                'attachment_path' => $this->attachment,
            ]);
        }
    }
}
