<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiRouting\Contracts\RouterInterface;
use TgBotApi\BotApiRouting\Contracts\RouteSetterInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;

class RouteSetterCollector
{
    private $routes;
    private $collection;

    /**
     * RouteCollector constructor.
     *
     * @param TelegramRouteCollection $collection
     * @param RouteSetterInterface[]  $routes
     */
    public function __construct(TelegramRouteCollection $collection, array $routes)
    {
        $this->routes[] = $routes;
        $this->collection = $collection;
    }

    public function collect(): void
    {
        foreach ($this->routes as $routeSetter) {
            $this->setRoutes($routeSetter);
        }
    }

    private function setRoutes(TelegramRouteInterface $setter): void
    {
        $setter->register($this->collection);
    }
}
