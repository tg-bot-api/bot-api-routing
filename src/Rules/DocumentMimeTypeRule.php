<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class DocumentMimeTypeRule extends IsDocumentRule
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
     */
    public function __construct(string $mimeType)
    {
        $this->mimeType = $mimeType;
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

        return (boolean)preg_match(
            sprintf('~^%s$~', $this->mimeType),
            $update->getUpdate()->message->document->mimeType
        );
    }
}
