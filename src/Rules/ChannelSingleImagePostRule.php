<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ChannelSingleImagePostRule
{
    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!($post = $update->getUpdate()->channelPost) || $post->mediaGroupId) {
            return false;
        }

        return $post->photo !== null;
    }
}
