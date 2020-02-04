<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Contracts\DTO\ParamRequest;
use TgBotApi\BotApiRouting\Exceptions\InvalidTypeException;
use TgBotApi\BotApiRouting\Exceptions\ParameterExtractionException;
use TgBotApi\BotApiRouting\Extractors\ArrayExtractor;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;
use TgBotApi\BotApiRouting\Stubs\ControllerStub;

class ReflectionParamResolverTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testResolveFromContextByType(): void
    {
        $container = new Context();
        $extractor = new ArrayExtractor();

        $request = ParamRequest::createFromClosure(
            $this->getRouterUpdate(),
            static function (ArrayExtractor $extractor) {
            }
        );

        $request->getUpdate()->getContext()->set(ArrayExtractor::class, $extractor);

        $resolver = new ReflectionParamResolver($container);

        $result = $resolver->resolve($request);

        $this->assertEquals([$extractor], $result);
    }

    public function testResolveFromContainerByType(): void
    {
        $container = new Context();
        $extractor = new ArrayExtractor();

        $request = ParamRequest::createFromClosure(
            $this->getRouterUpdate(),
            static function (ArrayExtractor $extractor) {
            }
        );

        $container->set(ArrayExtractor::class, $extractor);

        $resolver = new ReflectionParamResolver($container);

        $result = $resolver->resolve($request);

        $this->assertEquals([$extractor], $result);
    }

    public function testResolveFromContainerByName(): void
    {
        $container = new Context();
        $extractor = new ArrayExtractor();

        $request = ParamRequest::createFromClosure(
            $this->getRouterUpdate(),
            static function (
                ArrayExtractor $extractor,
                $anyValue,
                $nullValue,
                string $stringValue,
                ?ArrayExtractor $extractor2
            ) {
            }
        );

        $context = $request->getUpdate()->getContext();

        $context->set('extractor', $extractor);
        $context->set('anyValue', $extractor);
        $context->set('nullValue', null);
        $context->set('extractor2', null);
        $context->set('stringValue', 'string_value');

        $resolver = new ReflectionParamResolver($container);

        $result = $resolver->resolve($request);

        $this->assertEquals([$extractor, $extractor, null, 'string_value', null], $result);
    }

    public function testResolveFromContainerInterfaceNullNotAllowed(): void
    {
        $container = new Context();

        $request = ParamRequest::createFromClosure(
            $this->getRouterUpdate(),
            static function (ArrayExtractor $extractor) {
            }
        );

        $container->set(ArrayExtractor::class, null);

        $resolver = new ReflectionParamResolver($container);

        $this->expectException(InvalidTypeException::class);

        $resolver->resolve($request);
    }

    public function testResolveNotInContainers(): void
    {
        $container = new Context();

        $request = ParamRequest::createFromClosure(
            $this->getRouterUpdate(),
            static function (ArrayExtractor $extractor) {
            }
        );

        $resolver = new ReflectionParamResolver($container);

        $this->expectException(ParameterExtractionException::class);

        $resolver->resolve($request);
    }

    public function testResolveNoTypeMatchByName(): void
    {
        $container = new Context();

        $request = ParamRequest::createFromClosure(
            $this->getRouterUpdate(),
            static function (ArrayExtractor $extractor) {
            }
        );

        $resolver = new ReflectionParamResolver($container);

        $request->getUpdate()->getContext()->set('extractor', '$extractor');

        $this->expectException(ParameterExtractionException::class);

        $resolver->resolve($request);
    }

    public function testResolveNoTypeMatchByType(): void
    {
        $container = new Context();

        $request = ParamRequest::createFromClosure(
            $this->getRouterUpdate(),
            static function (ArrayExtractor $extractor) {
            }
        );

        $resolver = new ReflectionParamResolver($container);

        $request->getUpdate()->getContext()->set(ArrayExtractor::class, '$extractor');

        $this->expectException(ParameterExtractionException::class);

        $resolver->resolve($request);
    }

    public function testGetReflection(): void
    {
        $container = new Context();

        $request = ParamRequest::createFromMethodAndClass(
            $this->getRouterUpdate(),
            'method',
            new ControllerStub()
        );

        $resolver = new ReflectionParamResolver($container);

        $method = new \ReflectionMethod($resolver, 'getReflectionFunction');
        $method->setAccessible(true);
        $result = $method->invoke($resolver, $request);

        $this->assertInstanceOf(\ReflectionMethod::class, $result);
    }
}
