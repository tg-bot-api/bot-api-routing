<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ChannelPostTextRule
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
        return $update->getUpdate()->channelPost->text !== null;
    }
}
