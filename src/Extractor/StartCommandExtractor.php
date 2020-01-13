<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class StartCommandExtractor extends AbstractExtractor
{
    /**
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     * @throws RouteExtractionException
     */
    public function extract(RouterUpdateInterface $update, array $fields): void
    {
        foreach ($fields as $key => $command) {
            $this->checkContextAvailability($update->getContext(), $key);
            $update->getContext()->set(
                $key,
                $this->getCommandFromString($update->getUpdate()->message->text, $command)
            );
        }
    }

    private function getCommandFromString(string $message, string $command)
    {
        [$cmd, $value] = explode(' ', $message);
        if ($cmd !== '/start') {
            throw new \LogicException('command is not start');
        }

        $value = base64_decode($value);

        if (preg_match("/$command\((.+?)\)/", $value, $result) && count($result) === 2) {
            return $result[array_key_last($result)];
        }
        throw new \LogicException('cannot parse input please use format "varName(varValue)"');
    }
}
