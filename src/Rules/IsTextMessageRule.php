<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class IsTextMessageRule implements RouteRuleInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @return bool
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!($update->getUpdate()->message instanceof MessageType)) {
            return false;
        }

        return (bool)$update->getUpdate()->message->text;
    }
}
