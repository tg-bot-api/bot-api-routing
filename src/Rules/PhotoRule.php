<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class PhotoRule implements RouteRuleInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        $message = $update->getUpdate()->message;
        if (!$message->photo) {
            return false;
        }
        return true;
    }
}
