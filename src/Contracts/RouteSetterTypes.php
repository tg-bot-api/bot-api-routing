<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use TgBotApi\BotApiRouting\Interfaces\UpdateTypeTypes;

interface RouteSetterTypes extends UpdateTypeTypes
{
    public function register(TelegramRouteCollectionInterface $collection): void;
}
