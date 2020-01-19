<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class StartCommandExtractor extends AbstractExtractor
{
    /**
     * @param RouterUpdateInterface $update
     * @param string                $key
     * @param                       $field
     *
     * @return string
     *
     * @throws RouteExtractionException
     */
    protected function extractField(RouterUpdateInterface $update, string $key, $field): string
    {
        return $this->getCommandFromString($update->getUpdate()->message->text, $field);
    }

    /**
     * @param string $message
     * @param string $command
     *
     * @return string
     *
     * @throws RouteExtractionException
     *
     * @todo add message validation ^([a-zA-Z]+\(.+?\))((?1))((?1))((?1))$
     */
    private function getCommandFromString(string $message, string $command): string
    {
        [$cmd, $value] = explode(' ', $message);
        if ($cmd !== '/start') {
            throw new RouteExtractionException('Command is not start');
        }

        $value = base64_decode($value);

        if (preg_match("/$command\((.+?)\)/", $value, $result) && count($result) === 2) {
            return $result[\count($result) - 1];
        }

        throw new RouteExtractionException(
            sprintf(
                'Not found key`%s` in `%s`. Please use `key(value)` format',
                $command,
                $value
            )
        );
    }
}
