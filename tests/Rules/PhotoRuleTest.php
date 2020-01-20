<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\PhotoSizeType;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class PhotoRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatchSuccess(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new PhotoRule();
        $update->getUpdate()->message->photo = [new PhotoSizeType(), new PhotoSizeType()];
        $this->assertTrue($rule->match($update));
    }

    public function testNotMatchNullPhoto(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new PhotoRule();
        $this->assertFalse($rule->match($update));
    }

    public function testNotMatchBadPhotoSize(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new PhotoRule();
        $update->getUpdate()->message->photo = [new PhotoSizeType(), '', null];
        $this->assertFalse($rule->match($update));
    }
}
