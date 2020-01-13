<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ContentTextRule implements RouteRuleInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var bool
     */
    private $ignoreCase;

    /**
     * ContentTextRule constructor.
     *
     * @param string $text
     * @param bool   $ignoreCase
     */
    public function __construct(string $text, bool $ignoreCase = false)
    {
        $this->text = $text;
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        $message = $update->getUpdate()->message;
        $text = $message->text ?? $message->caption ?? '';
        return !$this->ignoreCase ? $text === $this->text : mb_strtolower($text) === mb_strtolower($this->text);
    }
}
