<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use Psr\Container\ContainerInterface;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Contracts\DTO\ParamRequest;
use TgBotApi\BotApiRouting\Contracts\ParamResolverInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\InvalidTypeException;
use TgBotApi\BotApiRouting\Exceptions\RouterParameterException;

class ReflectionParamResolver implements ParamResolverInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ParamRequest $paramRequest
     * @return array
     * @throws InvalidTypeException
     * @throws RouterParameterException
     * @throws \ReflectionException
     */
    public function resolve(ParamRequest $paramRequest): array
    {
        $params = $this->getInvokeParams($this->getReflectionFunction($paramRequest), $paramRequest->getUpdate());
        return $params ?: [];
    }

    /**
     * @param ParamRequest $paramRequest
     * @return ReflectionFunctionAbstract
     * @throws \ReflectionException
     */
    private function getReflectionFunction(ParamRequest $paramRequest): ReflectionFunctionAbstract
    {
        if ($paramRequest->getClosure()) {
            return new ReflectionFunction($paramRequest->getClosure());
        }

        return new \ReflectionMethod($paramRequest->getClass(), $paramRequest->getMethodName());
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

}
