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
        $post = $update->getUpdate()->channelPost;
        if (!$post || $post->mediaGroupId || !$post->caption) {
            return false;
        }

        return (bool)$post->photo;
    }
}
