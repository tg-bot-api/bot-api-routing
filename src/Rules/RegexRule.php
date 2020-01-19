<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class RegexRule implements RouteRuleInterface
{
    private $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return bool
     */
    public function match(RouterUpdateInterface $update): bool
    {
        $message = $update->getUpdate()->message;

        if (!$message->text && !$message->caption) {
            return false;
        }

        $text = $message->text ?? $message->caption;

        return mb_ereg_match($this->regex, $text);
    }
}
