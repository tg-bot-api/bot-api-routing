<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramResponseInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;
use TgBotApi\BotApiRouting\Exceptions\InvalidTypeException;
use TgBotApi\BotApiRouting\Exceptions\RouterParameterException;
use TgBotApi\BotApiRouting\Exceptions\RoutingException;
use TgBotApi\BotApiRouting\TelegramResponse;

class TelegramRouter extends AbstractTelegramRouter
{
    /**
     * @param RouterUpdateInterface $update
     *
     * @return TelegramResponse
     *
     * @throws InvalidTypeException
     * @throws RouterParameterException
     * @throws RoutingException
     * @throws ReflectionException
     */
    protected function invokeUpdate(RouterUpdateInterface $update): TelegramResponseInterface
    {
        if ($update->getActivatedRoute() === null) {
            throw new RoutingException(sprintf(
                'activatedRoute must be instance of TelegramRouterInterface, `%s` provided',
                getType($update->getActivatedRoute())
            ));
        }
        if ($update->getActivatedRoute()->getEndpoint() instanceof Closure) {
            $reflectionFunction = new ReflectionFunction($update->getActivatedRoute()->getEndpoint());
            return $reflectionFunction->invokeArgs($this->getInvokeParams($reflectionFunction, $update));
        }

        [$class, $method] = $this->getRouteClassAndMethod($update->getActivatedRoute());
        $reflectionMethod = new ReflectionMethod(implode('::', [$class, $method]));
        return $reflectionMethod->invokeArgs(
            $this->container->get($class),
            $this->getInvokeParams($reflectionMethod, $update)
        );
    }

    /**
     * Todo: Should be refactored
     *
     * @param ReflectionFunctionAbstract $reflectionMethod
     * @param RouterUpdateInterface      $update
     *
     * @return array
     *
     * @throws InvalidTypeException
     * @throws RouterParameterException
     */
    private function getInvokeParams(
        ReflectionFunctionAbstract $reflectionMethod,
        RouterUpdateInterface $update
    ): array {
        $reflectionParams = $reflectionMethod->getParameters();
        $params = [];
        foreach ($reflectionParams as $param) {
            $paramType = $param->getType();
            /** @var null|string $typeName */
            $typeName = $paramType instanceof ReflectionNamedType ? $paramType->getName() : null;
            if ($typeName && $update->getContext()->isSet((string)$typeName)) {
                $contextValue = $update->getContext()->get((string)$typeName);
                if ($contextValue instanceof $typeName || ($param->allowsNull() && $contextValue === null)) {
                    $params[] = $contextValue;
                    continue;
                }
                if ($contextValue === null && !$param->allowsNull()) {
                    throw new InvalidTypeException(sprintf(
                        'Null value not allowed for param \'%s\' in method %s in controller %s:%s',
                        $paramType . ' $' . $typeName,
                        $reflectionMethod->getName(),
                        $reflectionMethod->getFileName(),
                        $reflectionMethod->getStartLine()
                    ));
                }
            }

            if ($update->getContext()->isSet($param->getName())) {
                $contextValue = $update->getContext()->get($param->getName());
                if (!$paramType) {
                    $params[] = $contextValue;
                    continue;
                }
                if ($typeName === gettype($contextValue)) {
                    $params[] = $contextValue;
                    continue;
                }
                if ($contextValue instanceof $typeName) {
                    $params[] = $contextValue;
                    continue;
                }
            }

            if (!$paramType) {
                throw new RouterParameterException(sprintf(
                    'Param %s does not have the specified type.',
                    $typeName
                ));
            }

            if ($typeName === UpdateType::class) {
                $params[] = $update->getUpdate();
                continue;
            }

            if ($update instanceof $typeName) {
                $params[] = $update;
                continue;
            }

            if (!$typeName) {
                continue;
            }

            $message = '%s $%s is not public in container, make class public before using it in telegram controller.';

            if (!$this->container->has($typeName)) {
                throw new RouterParameterException(
                    sprintf(
                        $message,
                        $typeName,
                        $param->getName()
                    )
                );
            }

            $params[] = $this->container->get($typeName);
        }

        return $params;
    }

    /**
     * @param TelegramRouteInterface $route
     * @return array
     * @throws RouterParameterException
     */
    private function getRouteClassAndMethod(TelegramRouteInterface $route): array
    {
        $message = '%s is not valid class and method name. Please use class::method format or use Invokable class';
        $routeParts = explode('::', $route->getEndpoint());
        if (count($routeParts) > 2) {
            throw new RouterParameterException(sprintf(
                $message,
                $route->getEndpoint()
            ));
        }

        if (count($routeParts) === 2) {
            return $routeParts;
        }

        return [$routeParts[0], '__invoke'];
    }
}
