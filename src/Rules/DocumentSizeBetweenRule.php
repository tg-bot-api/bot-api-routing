<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class DocumentSizeBetweenRule extends IsDocumentRule
{
    private $min;

    private $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
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

        $size = $update->getUpdate()->message->document->fileSize;

        return ($size - $this->min) * ($size - $this->max) <= 0;
    }
}
