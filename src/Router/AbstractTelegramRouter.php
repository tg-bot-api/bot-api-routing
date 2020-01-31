<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use Psr\Container\ContainerInterface;
use TgBotApi\BotApiRouting\Contracts\ExtractorInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouterInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramResponseInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteCollectionInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

abstract class AbstractTelegramRouter implements TelegramRouterInterface
{
    /**
     * @var TelegramRouteCollectionInterface
     */
    protected $collection;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(TelegramRouteCollectionInterface $collection, ContainerInterface $container)
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
        if (!($update->getActivatedRoute() instanceof TelegramRouteInterface)) {
            throw new \LogicException('ActivatedRoute should be an instance of TelegramRouteInterface!');
        }

        foreach ($update->getActivatedRoute()->getExtractors() as [$extractor, $fields]) {
            if (is_string($extractor)) {
                $extractor = $this->container->has($extractor) ? $this->container->get($extractor) : new $extractor();
            }
            if (!($extractor instanceof ExtractorInterface)) {
                throw new RouteExtractionException('Extractor must implement ' . ExtractorInterface::class);
            }
            $extractor->extract($update, $fields);
        }
    }

}
