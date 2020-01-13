<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class CallBackQueryHasVar implements RouteRuleInterface
{
    private $regex;

    public function __construct(string $variableKey, string $type = '.+')
    {
        $this->regex = "/^$variableKey:$type$/";
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!$update->getUpdate()->callbackQuery->data) {
            return false;
        }

        return (bool)preg_match($this->regex, $update->getUpdate()->callbackQuery->data);
    }
}
