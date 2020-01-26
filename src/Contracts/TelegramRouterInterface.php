<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

interface TelegramRouterInterface
{
    public function dispatch(RouterUpdateInterface $update): ?TelegramResponseInterface;
}
