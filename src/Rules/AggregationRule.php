<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class AggregationRule implements RouteRuleInterface
{
    /**
     * @var RouteRuleInterface[]
     */
    private $rules;

    public function __construct(RouteRuleInterface ...$rules)
    {
        $this->rules = $rules;
    }


    /**
     * @param RouterUpdateInterface $update
     * @return bool
     */
    public function match(RouterUpdateInterface $update): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->match($update)) {
                return false;
            }
        }
        return true;
    }
}
