<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiRouting\Contracts\RouteSetterInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;

class RouteSetterCollector
{
    private $routeSetters;
    private $collection;

    /**
     * RouteCollector constructor.
     *
     * @param TelegramRouteCollection $collection
     * @param RouteSetterInterface[]  $routeSetters
     */
    public function __construct(TelegramRouteCollection $collection, array $routeSetters)
    {
        $this->routeSetters[] = $routeSetters;
        $this->collection = $collection;
    }

    public function collect(): void
    {
        foreach ($this->routeSetters as $routeSetter) {
            $this->setRoutes($routeSetter);
        }
    }

    private function setRoutes(RouteSetterInterface $setter): void
    {
        $setter->register($this->collection);
    }
}
