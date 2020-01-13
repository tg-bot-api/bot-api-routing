<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ChannelTextPostRule
{
    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!$update->getUpdate()->channelPost) {
            return false;
        }
        return (bool)$update->getUpdate()->channelPost->text;
    }
}
