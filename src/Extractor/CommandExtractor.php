<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionEmptyException;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class CommandExtractor extends AbstractExtractor
{
    protected $ignoreEmptyExtraction = true;

    /**
     * @param RouterUpdateInterface $update
     * @param string                $key
     * @param mixed                 $field
     * @return mixed|void
     * @throws RouteExtractionEmptyException
     */
    public function extractField(RouterUpdateInterface $update, string $key, $field)
    {
        $parts = explode(' ', $update->getUpdate()->message->text, 2);
        if (count($parts) < 2 || $parts[0] !== '/' . $field) {
            throw new RouteExtractionEmptyException('');
        }
        return $parts[1];
    }
}
