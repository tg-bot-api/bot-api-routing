<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\DocumentType;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class DocumentMimeTypeRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatch(): void
    {
        $rule = new DocumentMimeTypeRule('application/pdf');
        $this->assertTrue($rule->match($this->createUpdate()));
    }

    public function testNotMatch(): void
    {
        $rule = new DocumentMimeTypeRule('application/zip');
        $this->assertFalse($rule->match($this->createUpdate()));
    }

    public function testMatchRegex(): void
    {
        $rule = new DocumentMimeTypeRule('application/.*');
        $this->assertTrue($rule->match($this->createUpdate()));
    }

    public function testNotMatchRegex(): void
    {
        $rule = new DocumentMimeTypeRule('application/.');
        $this->assertFalse($rule->match($this->createUpdate()));
    }

    public function testNotUpdate(): void
    {
        $rule = new DocumentMimeTypeRule('application/.');
        $this->assertFalse($rule->match($this->getRouterUpdate()));
    }


    private function createUpdate(string $mimeType = 'application/pdf'): RouterUpdateInterface
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->document = new DocumentType();
        $update->getUpdate()->message->document->mimeType = $mimeType;

        return $update;
    }
}
