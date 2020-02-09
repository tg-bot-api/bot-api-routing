<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Router;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use TgBotApi\BotApiBase\Method\GetMeMethod;
use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteCollectionInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouterInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\Interfaces\UpdateTypeTypes;
use TgBotApi\BotApiRouting\Rules\AggregationRule;
use TgBotApi\BotApiRouting\Rules\IsTextMessageRule;
use TgBotApi\BotApiRouting\Rules\RegexMessageTextRule;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;
use TgBotApi\BotApiRouting\TelegramResponse;
use TgBotApi\BotApiRouting\TelegramRoute;
use TgBotApi\BotApiRouting\TelegramRouteCollection;

class AbstractTelegramRouterTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testDispatch(): void
    {
        $collection = $this->getCollection(new IsTextMessageRule());
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $router = $this->getAbstractRouterMock($collection, $this->getContainerWrapperMock());
        $router->method('invokeUpdate')
            ->willReturnCallback(static function (RouterUpdateInterface $routerUpdate) {
                return new TelegramResponse(GetMeMethod::create());
            });

        $this->assertInstanceOf(TelegramResponse::class, $router->dispatch($update));
    }

    public function testAnotherDispatch(): void
    {
        $collection = $this->getCollection(new AggregationRule(
            new RegexMessageTextRule('/^anotherText$/'),
            new IsTextMessageRule()
        ));
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'anotherText';
        $router = $this->getAbstractRouterMock($collection, $this->getContainerWrapperMock());

        $router->method('invokeUpdate')
            ->willReturnCallback(static function (RouterUpdateInterface $routerUpdate) {
                return new TelegramResponse(GetMeMethod::create());
            });

        $this->assertInstanceOf(TelegramResponse::class, $router->dispatch($update));
        $this->assertEquals($update->getUpdate()->message, $update->getContext()->get('message'));
        $this->assertEquals($update->getUpdate()->message->text, $update->getContext()->get('text'));
    }

    public function testNotDispatch(): void
    {
        $collection = $this->getCollection(new AggregationRule(
            new IsTextMessageRule(),
            new RegexMessageTextRule('/^text/')
        ));
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'Not Matched Text';

        $router = $this->getAbstractRouterMock($collection, $this->getContainerWrapperMock());

        $router->method('invokeUpdate')
            ->willReturnCallback(static function (RouterUpdateInterface $routerUpdate) {
                return new TelegramResponse(GetMeMethod::create());
            });

        $this->assertNull($router->dispatch($update));
    }

    public function testExtractionException(): void
    {
        $collection = $this->getCollection(new AggregationRule(
            new IsTextMessageRule(),
            new RegexMessageTextRule('/^text/')
        ));

        $route = new TelegramRoute(new IsTextMessageRule(), 'endpoint');
        $reflection = new ReflectionClass($route);
        $reflectionProperty = $reflection->getProperty('extractors');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($route, [\stdClass::class, ['varNameThree' => 'other.path.to.value']]);
        $collection->add($route);

        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'Text for second route';

        $router = $this->getAbstractRouterMock($collection, $this->getContainerWrapperMock());

        $this->expectException(RouteExtractionException::class);

        $router->dispatch($update);
    }

    public function testExtractionLogicException(): void
    {
        $collection = new TelegramRouteCollection();
        $route = $this->createMock(TelegramRoute::class);
        $route->method('match')->willReturn(true);
        $collection->add($route);

        $router = $this->getAbstractRouterMock($collection, $this->getContainerWrapperMock());

        $this->expectException(\LogicException::class);
        $this->assertInstanceOf(TelegramResponse::class, $router->dispatch($this->getRouterUpdate()));
    }

    private function getContainerWrapperMock(): ContainerInterface
    {
        return $this->createMock(ContainerInterface::class);
    }

    /**
     * @param TelegramRouteCollectionInterface $collection
     * @param ContainerInterface               $container
     * @return TelegramRouterInterface|MockObject
     */
    private function getAbstractRouterMock(
        TelegramRouteCollectionInterface $collection,
        ContainerInterface $container
    ): TelegramRouterInterface {
        return $this->getMockForAbstractClass(
            AbstractTelegramRouter::class,
            [$collection, $container]
        );
    }

    private function getCollection(RouteRuleInterface $rule): TelegramRouteCollection
    {
        $collection = new TelegramRouteCollection();
        $collection->add(new TelegramRoute($rule, 'endpoint'))
            ->extract(['text' => 'message.text', 'message' => 'message']);

        return $collection;
    }
}
