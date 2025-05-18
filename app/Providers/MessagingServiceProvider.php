<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\MessageStrategyFactory;
use App\Services\Messaging\DiscordStrategy;
use App\Services\Messaging\SlackStrategy;
use App\Services\Messaging\TelegramStrategy;
use App\Services\Messaging\WhatsappStrategy;

class MessagingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TelegramStrategy::class, function ($app) {
            return new TelegramStrategy(
                config('services.telegram.bot_token')
            );
        });
        $this->app->singleton(DiscordStrategy::class);
        $this->app->singleton(WhatsappStrategy::class);
        $this->app->singleton(SlackStrategy::class);
        
        $this->app->tag([
            TelegramStrategy::class,
            DiscordStrategy::class,
            WhatsappStrategy::class,
            SlackStrategy::class,
        ], 'message.strategies');

        $this->app->singleton(MessageStrategyFactory::class, function ($app) {
            return new MessageStrategyFactory(
                $app->tagged('message.strategies')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
