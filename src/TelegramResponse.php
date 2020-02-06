<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiBase\Method\Interfaces\MethodInterface;
use TgBotApi\BotApiRouting\Contracts\ContextInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramResponseInterface;

class TelegramResponse implements TelegramResponseInterface
{
    /**
     * @var MethodInterface
     */
    private $method;

    /**
     * @var callable[]
     */
    private $callbacks = [];

    public function __construct(MethodInterface $method)
    {
        $this->method = $method;
    }

    public function getTelegramMethod(): MethodInterface
    {
        return $this->method;
    }

    /**
     * @param callable $callback
     * @return TelegramResponseInterface
     */
    public function then(callable $callback): TelegramResponseInterface
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    public function resolve($response, ContextInterface $context): void
    {
        foreach ($this->callbacks as $callback) {
            $callback($response, $context);
        }
    }
}
