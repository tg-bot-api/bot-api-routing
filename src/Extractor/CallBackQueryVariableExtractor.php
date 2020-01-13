<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class CallBackQueryVariableExtractor extends AbstractExtractor
{
    /**
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     * @throws RouteExtractionException
     */
    public function extract(RouterUpdateInterface $update, array $fields): void
    {
        foreach ($fields as $key => $name) {
            $this->checkContextAvailability($update->getContext(), $key);
            $update->getContext()->set(
                $key,
                $this->extractData($update->getUpdate()->callbackQuery->data, $name)
            );
        }
    }

    private function extractData(string $data, string $name)
    {
        if (preg_match("/^$name:(.+?$)/", $data, $result) && count($result) === 2) {
            return $result[array_key_last($result)];
        }

        throw new \LogicException('cannot parse input please use format "varName(varValue)"');
    }
}
