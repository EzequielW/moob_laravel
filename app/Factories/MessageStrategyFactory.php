<?php

namespace App\Factories;

use App\Services\Interfaces\MessageStrategy;

class MessageStrategyFactory
{
    protected array $strategiesMap = [];

    public function __construct(protected iterable $strategies) {
        foreach ($strategies as $strategy) {
            $this->strategiesMap[$strategy->platformName()] = $strategy;
        }
    }

    public function make(string $platform): MessageStrategy
    {
        $platform = strtolower($platform);
        if (!isset($this->strategiesMap[$platform])) {
            throw new \InvalidArgumentException("Unsupported platform: {$platform}");
        }

        return $this->strategiesMap[$platform];
    }
}