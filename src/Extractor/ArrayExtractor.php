<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class ArrayExtractor extends AbstractExtractor
{
    /**
     * @param RouterUpdateInterface $update
     * @param string                $key
     * @param                       $path
     * @return mixed
     * @throws RouteExtractionException
     */
    public function extractField(RouterUpdateInterface $update, string $key, $path)
    {
        return $this->getValue($update->getUpdate(), $path);
    }

    /**
     * @param UpdateType $update
     * @param            $path
     * @return mixed|UpdateType
     * @throws RouteExtractionException
     */
    private function getValue(UpdateType $update, $path)
    {
        $result = $update;
        foreach (explode('.', $path) as $partial) {
            if (null === $result) {
                throw new RouteExtractionException(sprintf(
                    '`%s` partial of `%s` is null or not defined',
                    $partial,
                    $path
                ));
            }

            if (is_array($result)) {
                if (!isset($result[$partial])) {
                    throw new RouteExtractionException(
                        sprintf('Cannot access to property `%s` of array', $partial)
                    );
                }

                $result = $result[$partial];
                continue;
            }

            if (is_object($result)) {
                if (!property_exists($result, $partial)) {
                    throw new RouteExtractionException(
                        sprintf('Cannot access to property `%s` of %s', $partial, get_class($result))
                    );
                }
                $result = $result->$partial;
                continue;
            }

            throw new RouteExtractionException(
                sprintf('Cannot access to `%s` key on %s type', $partial, gettype($result))
            );
        }
        return $result;
    }
}
