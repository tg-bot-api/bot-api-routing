<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class CallBackQueryVariableExtractor extends AbstractExtractor
{
    protected function extractField(RouterUpdateInterface $update, string $key, $field)
    {
        $data = $update->getUpdate()->callbackQuery->data;

        if (preg_match(sprintf("/%s\((.+?)\)/", $field), $data, $result) && count($result) === 2) {
            return $result[count($result) - 1];
        }

        throw new RouteExtractionException(sprintf(
            'Not found key`%s` in `%s`. Please use `key(value)` format',
            $field,
            $data
        ));
    }
}
