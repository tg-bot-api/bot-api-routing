<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiRouting\Contracts\RouteSetterTypes;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\Extractors\ArrayExtractor;
use TgBotApi\BotApiRouting\Extractors\LiteralCommandExtractor;
use TgBotApi\BotApiRouting\Interfaces\UpdateTypeTypes;
use TgBotApi\BotApiRouting\Rules\ChatTypeRule;
use TgBotApi\BotApiRouting\Rules\IsTextMessageRule;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class TelegramRouteTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testGetUpdateType(): void
    {
        $route = $this->createRoute();
        $this->assertEquals(UpdateTypeTypes::TYPE_MESSAGE, $route->getUpdateType());
        $this->assertNotEquals(UpdateTypeTypes::TYPE_CHANNEL_POST, $route->getUpdateType());
    }

    public function testGetRules(): void
    {
        $route = $this->createRoute();
        $this->assertEquals([new IsTextMessageRule(), new ChatTypeRule([ChatType::TYPE_PRIVATE])], $route->getRules());
        $this->assertNotEquals([], $route->getRules());
    }

    public function testGetExtractors(): void
    {
        $extractor = new ArrayExtractor();

        $route = $this->createRoute();
        $route->extract(['varName' => 'path.to.value'], $extractor);
        $route->extract(['varNameTwo' => 'other.path.to.value'], $extractor);
        $route->extract(['command' => 'text'], LiteralCommandExtractor::class);
        $route->extract(['varNameThree' => 'other.path.to.value']);

        $this->assertEquals([
            [$extractor, ['varName' => 'path.to.value']],
            [$extractor, ['varNameTwo' => 'other.path.to.value']],
            [LiteralCommandExtractor::class, ['command' => 'text']],
            [ArrayExtractor::class, ['varNameThree' => 'other.path.to.value']]
        ], $route->getExtractors());
    }

    public function testExtractFail(): void
    {
        $route = $this->createRoute();
        $this->expectException(RouteExtractionException::class);
        $route->extract(['varName' => 'path.to.value'], new \stdClass());
    }

    public function testExtractFailSecond(): void
    {
        $route = $this->createRoute();
        $this->expectException(RouteExtractionException::class);
        $route->extract(['varNameTwo' => 'other.path.to.value'], \stdClass::class);
    }

    public function testExtractFailThird(): void
    {
        $route = $this->createRoute();
        $this->expectException(RouteExtractionException::class);
        $route->extract(['varNameTwo' => 'other.path.to.value'], 'k');
    }

    public function testExtractFailFourth(): void
    {
        $route = $this->createRoute();
        $this->expectException(RouteExtractionException::class);
        $route->extract(['varNameTwo' => 'other.path.to.value'], 10);
    }

    public function testGetEndpoint(): void
    {
        $route = $this->createRoute();

        $this->assertIsCallable($route->getEndpoint());
    }

    public function testMatch(): void
    {
        $update = $this->getRouterUpdate();
        $route = $this->createRoute();
        $update->getUpdate()->message->text = 'text';
        $update->getUpdate()->message->chat = new ChatType();
        $update->getUpdate()->message->chat->type = ChatType::TYPE_PRIVATE;

        $this->assertTrue($route->match($update));
    }

    public function testNotMatchChatType(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'text';
        $route = $this->createRoute();

        $this->assertFalse($route->match($update));
    }

    public function testNotMatchMessage(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->chat = new ChatType();
        $update->getUpdate()->message->chat->type = ChatType::TYPE_PRIVATE;
        $route = $this->createRoute();

        $this->assertFalse($route->match($update));
    }

    public function testNotMatchAll(): void
    {
        $update = $this->getRouterUpdate();
        $route = $this->createRoute();

        $this->assertFalse($route->match($update));
    }

    public function createRoute(): TelegramRoute
    {
        return new TelegramRoute(
            RouteSetterTypes::TYPE_MESSAGE,
            [new IsTextMessageRule(), new ChatTypeRule([ChatType::TYPE_PRIVATE])],
            static function ($update) {
                return $update;
            }
        );
    }
}
