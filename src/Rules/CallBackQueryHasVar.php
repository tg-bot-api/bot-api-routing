<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class CallBackQueryHasVar implements RouteRuleInterface
{
    private $regex = [];

    /**
     * CallBackQueryHasVar constructor.
     *
     * @param string[] $variableKey
     */
    public function __construct(array $variableKey)
    {
        foreach ($variableKey as $key => $value) {
            [$varName, $varMask] = is_int($key) ? [$value, '.+'] : [$key, $value ?: '.+'];
            $this->regex[] = sprintf("/%s\((%s?)\)/", $varName, $varMask);
        }
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!$update->getUpdate()->callbackQuery || !($data = $update->getUpdate()->callbackQuery->data)) {
            return false;
        }

        foreach ($this->regex as $regex) {
            if (!preg_match($regex, $data)) {
                return false;
            }
        }

        return true;
    }
}
