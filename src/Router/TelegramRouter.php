<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use Closure;
use Psr\Container\ContainerInterface;
use TgBotApi\BotApiRouting\Contracts\DTO\ParamRequest;
use TgBotApi\BotApiRouting\Contracts\ParamResolverInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramResponseInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteCollectionInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;
use TgBotApi\BotApiRouting\Exceptions\RoutingException;
use TgBotApi\BotApiRouting\TelegramResponse;

class TelegramRouter extends AbstractTelegramRouter
{
    /**
     * @var ParamResolverInterface
     */
    private $paramResolver;

    public function __construct(
        TelegramRouteCollectionInterface $collection,
        ContainerInterface $container,
        ParamResolverInterface $paramResolver
    ) {
        parent::__construct($collection, $container);
        $this->paramResolver = $paramResolver;
    }

    /**
     * @param RouterUpdateInterface $update
     *
     * @return TelegramResponse
     *
     * @throws RoutingException
     */
    protected function invokeUpdate(RouterUpdateInterface $update): ?TelegramResponseInterface
    {
        if ($update->getActivatedRoute() === null) {
            throw new RoutingException(sprintf(
                'activatedRoute must be instance of TelegramRouterInterface, `%s` provided',
                getType($update->getActivatedRoute())
            ));
        }

        $endpoint = $update->getActivatedRoute()->getEndpoint();

        if ($endpoint instanceof Closure) {
            return $endpoint(...$this->paramResolver->resolve(ParamRequest::createFromClosure($update, $endpoint)));
        }

        [$classId, $methodName] = $this->getControllerClassAndMethod($update->getActivatedRoute());

        if (!$this->container->has($classId)) {
            throw new RoutingException(sprintf(
                'Class with id `%s` not found in container.',
                $classId
            ));
        }

        $class = $this->container->get($classId);

        if (!is_object($class)) {
            throw new \TypeError(sprintf(
                'Container should return instance of controller, but `%s` returned.',
                getType($class)
            ));
        }

        if (!method_exists($class, $methodName)) {
            throw new RoutingException(sprintf(
                'Invalid class or method identifier, method `%s` not found in class `%s`',
                $methodName,
                get_class($class)
            ));
        }

        $params = $this->paramResolver->resolve(ParamRequest::createFromMethodAndClass($update, $endpoint, $class));

        return $class->$methodName(...$params);
    }

    /**
     * @param TelegramRouteInterface $route
     * @return array
     * @throws RoutingException
     */
    private function getControllerClassAndMethod(TelegramRouteInterface $route): array
    {
        $routeParts = explode('::', $route->getEndpoint());
        if (count($routeParts) > 2) {
            throw new RoutingException(sprintf(
                '`%s` is not valid class and method name. Please use class::method format or use Invokable class path.',
                $route->getEndpoint()
            ));
        }

        if (count($routeParts) === 2) {
            return $routeParts;
        }

        return [$routeParts[0], '__invoke'];
    }
}
