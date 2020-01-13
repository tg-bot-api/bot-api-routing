<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

interface RouteRuleInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool;
}
