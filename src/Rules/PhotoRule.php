<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiBase\Type\PhotoSizeType;
use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class PhotoRule implements RouteRuleInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @return bool
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!($photo = $update->getUpdate()->message->photo) || !is_array($photo)) {
            return false;
        }

        foreach ($update->getUpdate()->message->photo as $photoSizeType) {
            if (!($photoSizeType instanceof PhotoSizeType)) {
                return false;
            }
        }

        return true;
    }
}
