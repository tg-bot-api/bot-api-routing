<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use Psr\Container\ContainerInterface;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionParameter;
use TgBotApi\BotApiRouting\Contracts\DTO\ParamRequest;
use TgBotApi\BotApiRouting\Contracts\ParamResolverInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\InvalidTypeException;
use TgBotApi\BotApiRouting\Exceptions\ParameterExtractionException;

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
     * @throws ParameterExtractionException
     * @throws \ReflectionException
     */
    public function resolve(ParamRequest $paramRequest): array
    {
        return $this->getInvokeParams($this->getReflectionFunction($paramRequest), $paramRequest->getUpdate());
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
     * @throws ParameterExtractionException
     */
    private function getInvokeParams(
        ReflectionFunctionAbstract $reflectionMethod,
        RouterUpdateInterface $update
    ): array {
        $reflectionParams = $reflectionMethod->getParameters();
        $params = [];
        foreach ($reflectionParams as $param) {
            $message = '%s $%s is not public in container, and not found in context by type and by name.';

            try {
                $params[] = $this->tryToExtract($reflectionMethod, $update, $param);
            } catch (ParameterExtractionException $e) {
                throw new ParameterExtractionException(
                    sprintf(
                        $message,
                        $e->getMessage(),
                        $param->getName()
                    )
                );
            }
        }
        return $params;
    }

    /**
     * @param ReflectionFunctionAbstract $reflectionMethod
     * @param RouterUpdateInterface      $update
     * @param ReflectionParameter        $param
     * @return mixed|void|null
     * @throws InvalidTypeException
     * @throws ParameterExtractionException
     */
    private function tryToExtract(
        ReflectionFunctionAbstract $reflectionMethod,
        RouterUpdateInterface $update,
        ReflectionParameter $param
    ) {
        $typeName = $this->getParamTypeName($param);

        $containers = [];

        if ($typeName && !$this->isNotExtractableType($typeName)) {
            $containers = ['Context' => $update->getContext(), 'Container' => $this->container];
        }

        foreach ($containers as $container) {
            if ($container instanceof $typeName) {
                return $container;
            }
        }

        foreach ($containers as $key => $container) {
            try {
                $contextValue = $this->getParamFromContextByType(
                    $container,
                    $typeName,
                    $param->allowsNull()
                );
            } catch (InvalidTypeException $exception) {
                throw new InvalidTypeException(sprintf(
                    'Null value not allowed for param \'%s\' in method %s in controller %s:%s (null found in %s)',
                    $typeName . ' $' . $param->getName(),
                    $reflectionMethod->getName(),
                    $reflectionMethod->getFileName(),
                    $reflectionMethod->getStartLine(),
                    $key
                ));
            }
            if ($contextValue) {
                return $contextValue;
            }
        }

        if (!$update->getContext()->has($param->getName())) {
            throw new ParameterExtractionException($typeName);
        }

        $extractedFromContext = $update->getContext()->get($param->getName());

        if (!$typeName) {
            return $extractedFromContext;
        }

        if ($typeName === gettype($extractedFromContext)) {
            return $extractedFromContext;
        }

        if ($extractedFromContext instanceof $typeName) {
            return $extractedFromContext;
        }

        if ($extractedFromContext === null && $param->allowsNull()) {
            return null;
        }

        throw new ParameterExtractionException($typeName);
    }

    /**
     * @param ContainerInterface $container
     * @param string|null        $typeName
     * @param bool               $allowIsNull
     * @return mixed|void|null
     * @throws InvalidTypeException
     */
    private function getParamFromContextByType(
        ContainerInterface $container,
        ?string $typeName,
        bool $allowIsNull
    ) {
        if (!$typeName || !$container->has((string)$typeName)) {
            return;
        }

        $contextValue = $container->get((string)$typeName);

        if (null === $contextValue && !$allowIsNull) {
            throw new InvalidTypeException('');
        }

        if (null !== $contextValue && !($contextValue instanceof $typeName)) {
            return;
        }

        return $contextValue;
    }

    private function getParamTypeName(ReflectionParameter $param): ?string
    {
        $paramType = $param->getType();
        return $paramType instanceof ReflectionNamedType ? $paramType->getName() : null;
    }

    private function isNotExtractableType(string $type): bool
    {
        return in_array($type, [
            'integer',
            'float',
            'string',
            'boolean',
            'array',
            'object',
            'resource',
            'NULL',
            'null'
        ]);
    }
}
