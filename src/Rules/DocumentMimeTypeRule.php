<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class DocumentMimeTypeRule extends DocumentRule
{

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var bool
     */
    private $isRegex;

    /**
     * DocumentMimeTypeRule constructor.
     *
     * @param string $mimeType
     * @param bool   $isRegex
     */
    public function __construct(string $mimeType, $isRegex = false)
    {
        $this->mimeType = $mimeType;
        $this->isRegex = $isRegex;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return bool
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!parent::match($update)) {
            return false;
        }

        $document = $update->getUpdate()->message->document;

        return $this->isRegex ? mb_ereg_match($this->mimeType, $document->mimeType) : $document->mimeType === $this->mimeType;
    }
}
