<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\DocumentType;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class DocumentSizeBetweenRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatchBetween(): void
    {
        $rule = new DocumentSizeBetweenRule(100, 200);

        $this->assertTrue($rule->match($this->getUpdate(120)));
    }

    public function testMatchMin(): void
    {
        $rule = new DocumentSizeBetweenRule(100, 200);

        $this->assertTrue($rule->match($this->getUpdate(100)));
    }

    public function testMatchMax(): void
    {
        $rule = new DocumentSizeBetweenRule(100, 200);

        $this->assertTrue($rule->match($this->getUpdate(200)));
    }

    public function testMatchMore(): void
    {
        $rule = new DocumentSizeBetweenRule(100, 200);

        $this->assertFalse($rule->match($this->getUpdate(300)));
    }

    public function testMatchLess(): void
    {
        $rule = new DocumentSizeBetweenRule(100, 200);
        $this->assertFalse($rule->match($this->getUpdate(80)));
    }

    public function testMatchNoDocument(): void
    {
        $rule = new DocumentSizeBetweenRule(100, 200);
        $this->assertFalse($rule->match($this->getRouterUpdate()));
    }

    private function getUpdate($size): RouterUpdateInterface
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->document = new DocumentType();
        $update->getUpdate()->message->document->fileSize = $size;
        return $update;
    }

}
