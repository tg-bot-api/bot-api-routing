<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules\traits;

use TgBotApi\BotApiBase\Type\DocumentType;
use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiRouting\Rules\IsDocumentRule;

class IsDocumentRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testIsDocument(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->document = new DocumentType();

        $rule = new  IsDocumentRule();
        $this->assertTrue($rule->match($update));
    }

    public function testIsNotDocument(): void
    {
        $rule = new  IsDocumentRule();
        $this->assertFalse($rule->match($this->getRouterUpdate()));
    }
}
