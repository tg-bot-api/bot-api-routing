<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

interface TelegramRouteCollectionInterface
{
    /**
     * @param TelegramRouteInterface $route
     * @return TelegramRouteInterface
     */
    public function add(TelegramRouteInterface $route): TelegramRouteInterface;

    /**
     * @param string $updateType
     * @return TelegramRouteInterface[]
     */
    public function get(string $updateType): ?array;
}
