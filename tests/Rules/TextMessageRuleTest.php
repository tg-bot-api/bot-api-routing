<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class TextMessageRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatchSuccess(): void
    {
        $rule = new IsTextMessageRule();
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = 'someText';
        $this->assertTrue($rule->match($update));
    }

    public function testMatchFail(): void
    {
        $rule = new IsTextMessageRule();
        $this->assertFalse($rule->match($this->getRouterUpdate()));
    }

    public function testMatchFailNotMessage(): void
    {
        $rule = new IsTextMessageRule();
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message = null;
        $this->assertFalse($rule->match($update));
    }
}
