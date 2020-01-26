<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use TgBotApi\BotApiBase\Method\Interfaces\MethodInterface;

interface TelegramResponseInterface
{
    public function getTelegramRequest(): MethodInterface;

    public function getResponseType(): ?string;

    public function resolve($response, ContextInterface $context): void;

    /**
     * @param callable $callback
     * @return TelegramResponseInterface
     */
    public function then(callable $callback): TelegramResponseInterface;
}
