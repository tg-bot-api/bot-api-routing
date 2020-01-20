<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class DocumentFileNameRule implements RouteRuleInterface
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var bool
     */
    private $isRegex;

    /**
     * @var string
     */
    private $regexParams;

    private $documentRule;

    /**
     * DocumentFileNameRule constructor.
     *
     * @param string      $fileName
     * @param bool        $isRegex
     * @param string|null $regexParams
     */
    public function __construct(string $fileName, $isRegex = false, string $regexParams = null)
    {
        $this->fileName = $fileName;
        $this->isRegex = $isRegex;
        $this->regexParams = $regexParams;

        $this->documentRule = new DocumentRule();
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!$this->documentRule->match($update)) {
            return false;
        }

        $document = $update->getUpdate()->message->document;

        return $this->isRegex ? mb_ereg_match($this->fileName, $document->fileName, $this->regexParams) : $document->fileName === $this->fileName;
    }
}
