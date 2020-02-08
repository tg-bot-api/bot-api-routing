<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiRouting\Contracts\TelegramRouteCollectionInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;

class TelegramRouteCollection implements TelegramRouteCollectionInterface
{
    /**
     * @var TelegramRouteInterface[]
     */
    private $collection = [];

    /**
     * TelegramRouteCollection constructor.
     *
     * @param TelegramRouteInterface[] $routes
     */
    public function __construct(iterable $routes = [])
    {
        foreach ($routes as $route) {
            $this->add($route);
        }
    }

    /**
     * @param TelegramRouteInterface $route
     * @return TelegramRoute
     */
    public function add(TelegramRouteInterface $route): TelegramRouteInterface
    {
        $this->collection[] = $route;
        return $route;
    }

    /**
     * @return TelegramRoute[]
     */
    public function get(): array
    {
        return $this->collection;
    }
}
