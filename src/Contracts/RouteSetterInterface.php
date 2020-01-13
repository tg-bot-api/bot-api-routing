<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;


interface RouteSetterInterface
{
    public function register(TelegramRouteCollectionInterface $collection): void;
}
