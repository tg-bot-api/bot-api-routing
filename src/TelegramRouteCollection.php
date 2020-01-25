<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiRouting\Contracts\TelegramRouteCollectionInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;

class TelegramRouteCollection implements TelegramRouteCollectionInterface
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * @param TelegramRouteInterface $route
     * @return TelegramRoute
     */
    public function add(TelegramRouteInterface $route): TelegramRouteInterface
    {
        $this->collection[$route->getUpdateType()][] = $route;
        return $route;
    }

    /**
     * @param string $updateType
     * @return TelegramRoute[]
     */
    public function get(string $updateType): ?array
    {
        return $this->collection[$updateType] ?? null;
    }
}
