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
     * @param array                 $fields
     * @return void
     * @throws RouteExtractionException
     */
    public function extract(RouterUpdateInterface $update, array $fields): void
    {
        foreach ($fields as $key => $path) {
            $this->checkContextAvailability($update->getContext(), $key);
            $update->getContext()->set($key, $this->getValue($update->getUpdate(), $path));
        }
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
            if (!$result) {
                throw new RouteExtractionException(sprintf('%s partial of %s is not defined', $partial, $path));
            }
            if (is_array($result)) {
                $result = $result[$partial];
                continue;
            }
            if (is_object($result)) {
                if (!property_exists($result, $partial)) {
                    throw new RouteExtractionException(sprintf('Cannot access to property %s of %s', $partial, get_class($result)));
                }
                $result = $result->$partial;
                continue;
            }
            throw new RouteExtractionException(sprintf('Cannot access to %s key on %s type', $partial, gettype($result)));
        }
        return $result;
    }
}
