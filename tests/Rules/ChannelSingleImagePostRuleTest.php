<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiBase\Type\PhotoSizeType;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class ChannelSingleImagePostRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatch(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->channelPost = new MessageType();
        $update->getUpdate()->channelPost->photo = [new PhotoSizeType()];

        $rule = new ChannelSingleImagePostRule();

        $this->assertTrue($rule->match($update));
    }

    public function testNoPhoto(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->channelPost = new MessageType();

        $rule = new ChannelSingleImagePostRule();

        $this->assertFalse($rule->match($update));
    }

    public function testNotSinglePhoto(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->channelPost = new MessageType();
        $update->getUpdate()->channelPost->mediaGroupId = 'id';

        $rule = new ChannelSingleImagePostRule();

        $this->assertFalse($rule->match($update));
    }
}
