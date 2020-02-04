<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use TgBotApi\BotApiRouting\Contracts\ContextInterface;
use TgBotApi\BotApiRouting\Contracts\ExtractorInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionEmptyException;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

abstract class AbstractExtractor implements ExtractorInterface
{
    protected $ignoreEmptyExtraction = false;

    /**
     * @param RouterUpdateInterface $update
     * @param string                $key
     * @param                       $field
     * @return mixed
     * @throws RouteExtractionEmptyException
     * @throws RouteExtractionException
     */
    abstract protected function extractField(RouterUpdateInterface $update, string $key, $field);

    /**
     * @param RouterUpdateInterface $update
     * @param array|null            $fields
     * @return mixed|void
     * @throws RouteExtractionException
     */
    public function extract(RouterUpdateInterface $update, ?array $fields = null): void
    {
        if (!$fields) {
            $fields = [static::class => null];
        }

        foreach ($fields as $key => $field) {
            $this->checkContextAvailability($update->getContext(), $key);

            $extractedField = null;

            try {
                $extractedField = $this->extractField($update, $key, $field);
            } catch (RouteExtractionEmptyException $e) {
                if (!$this->ignoreEmptyExtraction) {
                    throw new RouteExtractionException(sprintf(
                        'Extraction failed: extraction with key %s is null',
                        $key
                    ));
                }
            }

            $update->getContext()->set($key, $extractedField);
        }
    }

    /**
     * @param ContextInterface $context
     * @param string           $key
     * @throws RouteExtractionException
     */
    protected function checkContextAvailability(ContextInterface $context, string $key): void
    {
        if ($context->has($key)) {
            throw new RouteExtractionException(sprintf('%s variable already set in context', $key));
        }
    }
}
