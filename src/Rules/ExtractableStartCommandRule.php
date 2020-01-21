<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteRuleException;

class ExtractableStartCommandRule extends ExtractableCommandRule
{
    /**
     * ExtractableStartCommandRule constructor.
     *
     * @param array $variables
     * @param bool  $keepOrder
     * @throws RouteRuleException
     */
    public function __construct(array $variables = [], bool $keepOrder = false)
    {
        parent::__construct('start', $variables, $keepOrder);
    }
}
