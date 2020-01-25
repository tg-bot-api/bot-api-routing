<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class ChannelPostTextRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatch(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->channelPost = new MessageType();
        $update->getUpdate()->channelPost->text = '';

        $rule = new ChannelPostTextRule();

        $this->assertTrue($rule->match($update));
    }

    public function testNoText(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->channelPost = new MessageType();

        $rule = new ChannelPostTextRule();

        $this->assertFalse($rule->match($update));
    }

    public function testNoPostField(): void
    {
        $update = $this->getRouterUpdate();

        $rule = new ChannelPostTextRule();

        $this->assertFalse($rule->match($update));
    }
}
