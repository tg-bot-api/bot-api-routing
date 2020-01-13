<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules\Custom;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ExtractableStartCommandRule implements RouteRuleInterface
{
    private $variables;

    /**
     * ExtractableStartCommandRule constructor.
     *
     * @param string[] $variables
     */
    public function __construct(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!$update->getUpdate()->message) {
            return false;
        }

        $parts = explode(' ', $update->getUpdate()->message->text);
        if (count($parts) < 2 || $parts[0] !== '/start') {
            return false;
        }

        $value = base64_decode($parts[1]);

        foreach ($this->variables as $variable) {
            if (!preg_match("~$variable\((.+?)\)~", $value)) {
                return false;
            }
        }

        return true;
    }
}
