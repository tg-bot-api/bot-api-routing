<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules\Custom;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ExtractableMessageRule implements RouteRuleInterface
{
    private $command;

    /**
     * ExtractableStartCommandRule constructor.
     *
     * @param string $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function match(RouterUpdateInterface $update): bool
    {
        $parts = explode(' ', $update->getUpdate()->message->text);
        return !(count($parts) < 2 || $parts[0] !== '/' . $this->command);
    }
}
