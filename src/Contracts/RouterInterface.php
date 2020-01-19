<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

interface RouterInterface
{
    public function dispatch(RouterUpdateInterface $update): ?TelegramResponseInterface;
}
