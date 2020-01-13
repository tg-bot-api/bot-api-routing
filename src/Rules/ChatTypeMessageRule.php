<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ChatTypeMessageRule implements RouteRuleInterface
{

    private $types;

    /**
     * ChatTypeMessageRule constructor.
     *
     * @param string[] $types
     * @param bool     $exclude
     */
    public function __construct(array $types, bool $exclude = false)
    {
        if ($exclude) {
            $allowedTypes = [
                ChatType::TYPE_PRIVATE,
                ChatType::TYPE_CHANNEL,
                ChatType::TYPE_GROUP,
                ChatType::TYPE_SUPERGROUP
            ];
            $types = array_diff($allowedTypes, $types);
        }
        $this->types = $types;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!$update->getUpdate()->message->chat || !$update->getUpdate()->message->chat->type) {
            return false;
        }

        return in_array($update->getUpdate()->message->chat->type, $this->types, true);
    }
}
