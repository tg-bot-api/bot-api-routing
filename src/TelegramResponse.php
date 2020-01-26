<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiBase\Method\Interfaces\MethodInterface;
use TgBotApi\BotApiRouting\Contracts\ContextInterface;
use TgBotApi\BotApiRouting\Contracts\ResponseClosure;
use TgBotApi\BotApiRouting\Contracts\TelegramResponseInterface;

class TelegramResponse implements TelegramResponseInterface
{

    /**
     * @var MethodInterface
     */
    private $method;
    /**
     * @var string|null
     */
    private $responseType;
    /**
     * @var callable[]
     */
    private $callbacks;

    public function __construct(MethodInterface $method, ?string $responseType = null, callable $callback = null)
    {
        $this->method = $method;
        $this->responseType = $responseType;
        $this->callbacks[] = $callback;
    }

    public function getTelegramRequest(): MethodInterface
    {
        return $this->method;
    }

    public function getResponseType(): ?string
    {
        return $this->responseType;
    }

    public function getCallbacks(): array
    {
        return $this->callbacks;
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
