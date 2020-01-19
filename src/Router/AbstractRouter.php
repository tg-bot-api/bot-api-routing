<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use TgBotApi\BotApiRouting\Contracts\ContainerWrapperInterface;
use TgBotApi\BotApiRouting\Contracts\ExtractorInterface;
use TgBotApi\BotApiRouting\Contracts\RouterInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramResponseInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteCollectionInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

abstract class AbstractRouter implements RouterInterface
{
    protected $collection;

    protected $container;

    public function __construct(TelegramRouteCollectionInterface $collection, ContainerWrapperInterface $container)
    {
        $this->collection = $collection;
        $this->container = $container;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return TelegramResponseInterface
     */
    abstract protected function invokeUpdate(RouterUpdateInterface $update): TelegramResponseInterface;

    /**
     * @param RouterUpdateInterface $update
     * @return TelegramResponseInterface|null
     * @throws RouteExtractionException
     */
    public function dispatch(RouterUpdateInterface $update): ?TelegramResponseInterface
    {
        if ($this->collection->get($update->getType())) {
            foreach ($this->collection->get($update->getType()) as $route) {
                if ($route->match($update)) {
                    $this->extractRouteData($update);
                    return $this->invokeUpdate($update);
                }
            }
        }
        return null;
    }

    /**
     * @param RouterUpdateInterface $update
     * @throws RouteExtractionException
     */
    protected function extractRouteData(RouterUpdateInterface $update): void
    {
        foreach ($update->getRoute()->getExtractors() as [$extractor, $fields]) {
            if (is_string($extractor)) {
                $extractor = $this->container->get($extractor);
            }
            if (!($extractor instanceof ExtractorInterface)) {
                throw new RouteExtractionException('Extractor must be implement ' . ExtractorInterface::class);
            }
            $extractor->extract($update, $fields);
        }
    }

}
