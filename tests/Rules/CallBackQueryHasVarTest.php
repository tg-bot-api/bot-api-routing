<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\CallbackQueryType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\RouterUpdate;

class CallBackQueryHasVarTest extends TestCase
{

    public function testMatchSimpleArray(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new CallBackQueryHasVar(['anotherString', 'someString']);
        $this->assertTrue($rule->match($update));
    }

    public function testMatchTypedArray(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new CallBackQueryHasVar(['anotherString' => '[a-z]+', 'numberString' => '\d+']);
        $this->assertTrue($rule->match($update));
    }

    public function testNotMatchTypedArray(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new CallBackQueryHasVar(['anotherString' => '\d+', 'numberString' => '[a-z]+']);
        $this->assertFalse($rule->match($update));
    }

    public function testNotMatchTypedArraySecondOption(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new CallBackQueryHasVar(['numberString' => '[a-z]+']);
        $this->assertFalse($rule->match($update));
    }

    public function testNotMatchVarName(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new CallBackQueryHasVar(['badString']);
        $this->assertFalse($rule->match($update));
    }

    public function testNotCallbackQuery(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $update = new RouterUpdate($updateType, $context);
        $rule = new CallBackQueryHasVar(['badString']);
        $this->assertFalse($rule->match($update));
    }

    public function testNotCallbackQueryData(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->callbackQuery = new CallbackQueryType();
        $update = new RouterUpdate($updateType, $context);
        $rule = new CallBackQueryHasVar(['badString']);
        $this->assertFalse($rule->match($update));
    }

    private function getRouterUpdate(): RouterUpdate
    {
        $text = 'someString(someData)anotherString(another)numberString(123445637)';
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->callbackQuery = new CallbackQueryType();
        $updateType->callbackQuery->data = $text;
        return new RouterUpdate($updateType, $context);
    }
}
