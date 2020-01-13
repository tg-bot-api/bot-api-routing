<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;


use TgBotApi\BotApiRouting\TelegramRoute;

interface TelegramRouteCollectionInterface
{
    /**
     * @param TelegramRoute $route
     * @return TelegramRoute
     */
    public function add(TelegramRoute $route): TelegramRoute;

    /**
     * @param string $updateType
     * @return TelegramRoute[]
     */
    public function get(string $updateType): ?array;
}
