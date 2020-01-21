<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class DocumentFileNameRule extends IsDocumentRule
{
    /**
     * @var string
     */
    private $fileName;

    private $isRegex;

    /**
     * DocumentFileNameRule constructor.
     *
     * @param string $fileName
     * @param bool   $isRegex
     */
    public function __construct(string $fileName, bool $isRegex = false)
    {
        $this->fileName = $fileName;
        $this->isRegex = $isRegex;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!parent::match($update)) {
            return false;
        }

        $name = $update->getUpdate()->message->document->fileName;

        return $this->isRegex ? (bool)preg_match(sprintf('/^%s$/', $this->fileName), $name) : $this->fileName === $name;
    }
}
