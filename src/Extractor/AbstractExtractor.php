<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiRouting\Contracts\ContextInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\TelegramRouter\Extractor\ExtractorInterface;

abstract class AbstractExtractor implements ExtractorInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     * @throws RouteExtractionException
     */
    abstract public function extract(RouterUpdateInterface $update, array $fields): void;

    /**
     * @param ContextInterface $context
     * @param string           $key
     * @throws RouteExtractionException
     */
    protected function checkContextAvailability(ContextInterface $context, string $key): void
    {
        if ($context->isSet($key)) {
            throw new RouteExtractionException(sprintf('%s variable already set in context', $key));
        }
    }
}
