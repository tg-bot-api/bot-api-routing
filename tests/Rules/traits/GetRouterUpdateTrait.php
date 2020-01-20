<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules\traits;

use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\RouterUpdate;

trait GetRouterUpdateTrait
{
    public function getRouterUpdate(): RouterUpdateInterface
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        return new RouterUpdate($updateType, $context);
    }
}
