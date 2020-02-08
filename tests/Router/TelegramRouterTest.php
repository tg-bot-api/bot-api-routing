<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use TgBotApi\BotApiRouting\Contracts\DTO\ParamRequest;
use TgBotApi\BotApiRouting\Contracts\ParamResolverInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteCollectionInterface;
use TgBotApi\BotApiRouting\Exceptions\RoutingException;
use TgBotApi\BotApiRouting\Interfaces\UpdateTypeTypes;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;
use TgBotApi\BotApiRouting\Stubs\ControllerStub;
use TgBotApi\BotApiRouting\TelegramRoute;
use TgBotApi\BotApiRouting\TelegramRouteCollection;

class TelegramRouterTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testDispatchNoActivatedRoute(): void
    {
        $collection = $this->getCollection([]);
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $router = new TelegramRouter($collection, $this->getContainerWrapperMock(), $this->getResolver());
        $method = new ReflectionMethod(TelegramRouter::class, 'invokeUpdate');
        $method->setAccessible(true);

        $this->expectException(RoutingException::class);

        $method->invoke($router, $update);
    }

    public function testDispatchCallback(): void
    {
        $collection = $this->getCollection([]);
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $resolver = $this->getResolver([$update->getUpdate()->message]);
        $router = new TelegramRouter($collection, $this->getContainerWrapperMock(), $resolver);

        $method = new ReflectionMethod(TelegramRouter::class, 'invokeUpdate');
        $method->setAccessible(true);

        $update->setActivatedRoute(new TelegramRoute([], static function ($message) {
            Assert::assertEquals($message->text, 'text');
        }));

        $update->getContext()->set('message', $update->getUpdate()->message);

        $method->invoke($router, $update);
    }

    public function testDispatchStub(): void
    {
        $controllerStub = $this->createMock(ControllerStub::class);
        $controllerStub->expects($this->once())->method('method')->willReturn(null);

        $collection = $this->getCollection([]);
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $container = $this->getContainerWrapperMock();
        $container->method('get')->willReturn($controllerStub);
        $container->method('has')->willReturn(true);

        $router = new TelegramRouter($collection, $container, $this->getResolver());

        $method = new ReflectionMethod(TelegramRouter::class, 'invokeUpdate');
        $method->setAccessible(true);

        $update->setActivatedRoute(new TelegramRoute([], 'path::method'));

        $method->invoke($router, $update);
    }

    public function testDispatchCallableController(): void
    {
        $controllerStub = $this->createMock(ControllerStub::class);
        $controllerStub->expects($this->once())->method('__invoke')->willReturn(null);

        $collection = $this->getCollection([]);
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $container = $this->getContainerWrapperMock();
        $container->method('get')->willReturn($controllerStub);
        $container->method('has')->willReturn(true);


        $resolver = $this->createMock(ParamResolverInterface::class);

        $resolver->expects($this->once())
            ->method('resolve')
            ->willReturnCallback(static function (ParamRequest $paramRequest) use ($update, $controllerStub) {
                TestCase::assertEquals($controllerStub, $paramRequest->getClass());
                TestCase::assertEquals('__invoke', $paramRequest->getMethodName());
                TestCase::assertEquals($update, $paramRequest->getUpdate());
                return [];
            });

        $router = new TelegramRouter($collection, $container, $resolver);

        $method = new ReflectionMethod(TelegramRouter::class, 'invokeUpdate');
        $method->setAccessible(true);

        $update->setActivatedRoute(new TelegramRoute([], 'path'));

        $method->invoke($router, $update);
    }

    public function testDispatchStubMethodNotFound(): void
    {
        $controllerStub = $this->createMock(ControllerStub::class);

        $collection = $this->getCollection([]);
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $container = $this->getContainerWrapperMock();
        $container->method('get')->willReturn($controllerStub);
        $container->method('has')->willReturn(true);

        $router = new TelegramRouter($collection, $container, $this->getResolver());

        $method = new ReflectionMethod(TelegramRouter::class, 'invokeUpdate');
        $method->setAccessible(true);

        $update->setActivatedRoute(new TelegramRoute([], 'path::badMethod'));

        $this->expectException(RoutingException::class);

        $method->invoke($router, $update);
    }

    public function testDispatchStubBadClass(): void
    {
        $collection = $this->getCollection([]);
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $container = $this->getContainerWrapperMock();
        $container->method('get')->willReturn([]);
        $container->method('has')->willReturn(true);

        $router = new TelegramRouter($collection, $container, $this->getResolver());

        $update->setActivatedRoute(new TelegramRoute([], 'path::badMethod'));

        $method = new ReflectionMethod(TelegramRouter::class, 'invokeUpdate');
        $method->setAccessible(true);

        $this->expectException(\TypeError::class);

        $method->invoke($router, $update);
    }

    public function testDispatchNoControllerInContainer(): void
    {
        $collection = $this->getCollection([]);
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $container = $this->getContainerWrapperMock();
        $container->method('has')->willReturn(null);

        $router = new TelegramRouter($collection, $container, $this->getResolver());

        $update->setActivatedRoute(new TelegramRoute([], 'path::method'));

        $method = new ReflectionMethod(TelegramRouter::class, 'invokeUpdate');
        $method->setAccessible(true);

        $this->expectException(RoutingException::class);

        $method->invoke($router, $update);
    }

    public function testGetControllerClassAndMethod(): void
    {
        $router = new TelegramRouter(
            $this->createMock(TelegramRouteCollectionInterface::class),
            $this->getContainerWrapperMock(),
            $this->getResolver()
        );

        $method = new ReflectionMethod(TelegramRouter::class, 'getControllerClassAndMethod');
        $method->setAccessible(true);

        $result = $method->invoke(
            $router,
            new TelegramRoute( [], 'class::method')
        );

        $this->assertEquals($result, ['class', 'method']);
    }

    public function testGetControllerInvokable(): void
    {
        $router = new TelegramRouter(
            $this->createMock(TelegramRouteCollectionInterface::class),
            $this->getContainerWrapperMock(),
            $this->getResolver()
        );

        $method = new ReflectionMethod(TelegramRouter::class, 'getControllerClassAndMethod');
        $method->setAccessible(true);

        $result = $method->invoke(
            $router,
            new TelegramRoute([], 'class')
        );

        $this->assertEquals($result, ['class', '__invoke']);
    }

    public function testBadPathGetControllerClassAndMethod(): void
    {
        $router = new TelegramRouter(
            $this->createMock(TelegramRouteCollectionInterface::class),
            $this->getContainerWrapperMock(),
            $this->getResolver()
        );

        $method = new ReflectionMethod(TelegramRouter::class, 'getControllerClassAndMethod');
        $method->setAccessible(true);

        $this->expectException(RoutingException::class);

        $method->invoke(
            $router,
            new TelegramRoute([], 'class::method::i')
        );
    }

    private function getResolver($response = []): ParamResolverInterface
    {
        $resolver = $this->createMock(ParamResolverInterface::class);
        $resolver->method('resolve')->willReturn($response);
        return $resolver;
    }

    /**
     * @return ContainerInterface|MockObject
     */
    private function getContainerWrapperMock(): ContainerInterface
    {
        return $this->createMock(ContainerInterface::class);
    }

    private function getCollection(array $rules): TelegramRouteCollection
    {
        $collection = new TelegramRouteCollection();
        $collection->add(new TelegramRoute($rules, 'endpoint'))
            ->extract(['text' => 'message.text', 'message' => 'message']);

        return $collection;
    }
}
