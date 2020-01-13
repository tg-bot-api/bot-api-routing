<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class CommandExtractor extends AbstractExtractor
{
    /**
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     * @throws RouteExtractionException
     */
    public function extract(RouterUpdateInterface $update, array $fields): void
    {
        foreach ($fields as $key => $field) {
            $parts = explode(' ', $update->getUpdate()->message->text, 2);
            if (count($parts) < 2 || $parts[0] !== '/' . $field) {
                continue;
            }
            $this->checkContextAvailability($update->getContext(), $key);
            $update->getContext()->set($key, $parts[1]);
        }

    }
}
