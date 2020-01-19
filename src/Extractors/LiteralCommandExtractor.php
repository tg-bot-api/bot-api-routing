<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class LiteralCommandExtractor extends AbstractExtractor
{
    protected $ignoreEmptyExtraction = true;

    /**
     * @param RouterUpdateInterface $update
     * @param string                $key
     * @param mixed                 $field
     * @return mixed|void
     * @throws RouteExtractionException
     */
    public function extractField(RouterUpdateInterface $update, string $key, $field)
    {
        $parts = explode(' ', $update->getUpdate()->message->text, 2);
        if (count($parts) < 2 || $parts[0] !== '/' . $field) {
            throw new RouteExtractionException(sprintf(
                'Command `%s` not found in message `%s`',
                $field,
                $update->getUpdate()->message->text
            ));
        }
        return $parts[1];
    }
}
