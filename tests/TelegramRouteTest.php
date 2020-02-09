<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\Extractors\ArrayExtractor;
use TgBotApi\BotApiRouting\Extractors\LiteralCommandExtractor;
use TgBotApi\BotApiRouting\Rules\AggregationRule;
use TgBotApi\BotApiRouting\Rules\ChatTypeRule;
use TgBotApi\BotApiRouting\Rules\IsTextMessageRule;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class TelegramRouteTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testWeight(): void
    {
        $route = new TelegramRoute(new AggregationRule(), 'endpoint');
        $route2 = new TelegramRoute(new AggregationRule(), 'endpoint', 1);
        $route3 = new TelegramRoute(new AggregationRule(), 'endpoint', 2);

        $this->assertEquals($route->getWeight(), 0);
        $this->assertEquals($route2->getWeight(), 1);
        $this->assertEquals($route3->getWeight(), 2);
    }

    public function testGetRules(): void
    {
        $rule = new IsTextMessageRule();
        $route = $this->createRoute($rule);
        $this->assertEquals($rule, $route->getRule());
        $this->assertNotNull($route->getRule());
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

    public function createRoute(RouteRuleInterface $rule = null): TelegramRoute
    {
        return new TelegramRoute(
            $rule ?: new AggregationRule(new IsTextMessageRule(), new ChatTypeRule([ChatType::TYPE_PRIVATE])),
            static function ($update) {
                return $update;
            }
        );
    }
}
