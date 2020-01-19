<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiBase\Method\Interfaces\MethodInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramResponseInterface;

class TelegramResponse implements TelegramResponseInterface
{

    private $method;
    private $responseType;
    private $callback;

    public function __construct(MethodInterface $method, ?string $responseType = null, callable $callback = null)
    {
        $this->method = $method;
        $this->responseType = $responseType;
        $this->callback = $callback;
    }

    public function getTelegramRequest(): MethodInterface
    {
        return $this->method;
    }

    public function getResponseType(): ?string
    {
        return $this->responseType;
    }

    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    public function then(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }
}
