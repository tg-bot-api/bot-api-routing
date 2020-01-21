<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\DocumentType;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class DocumentFileNameRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatch(): void
    {
        $rule = new DocumentFileNameRule('filename.ext');
        $this->assertTrue($rule->match($this->createUpdate()));
    }

    public function testNotMatch(): void
    {
        $rule = new DocumentFileNameRule('badFilename.ext');
        $this->assertFalse($rule->match($this->createUpdate()));
    }

    public function testMatchRegex(): void
    {
        $rule = new DocumentFileNameRule('filename\..*', true);
        $this->assertTrue($rule->match($this->createUpdate()));

        $rule = new DocumentFileNameRule('filename\.ext', true);
        $this->assertTrue($rule->match($this->createUpdate()));

        $rule = new DocumentFileNameRule('.*\.ext', true);
        $this->assertTrue($rule->match($this->createUpdate()));
    }

    public function testNotMatchRegex(): void
    {
        $rule = new DocumentFileNameRule('filename\.any', true);
        $this->assertFalse($rule->match($this->createUpdate()));

        $rule = new DocumentFileNameRule('not-matched', true);
        $this->assertFalse($rule->match($this->createUpdate()));

        $rule = new DocumentFileNameRule('filename', true);
        $this->assertFalse($rule->match($this->createUpdate()));
    }

    public function testNotUpdate(): void
    {
        $rule = new DocumentFileNameRule('');
        $this->assertFalse($rule->match($this->getRouterUpdate()));
    }

    private function createUpdate(string $mimeType = 'filename.ext'): RouterUpdateInterface
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->document = new DocumentType();
        $update->getUpdate()->message->document->fileName = $mimeType;

        return $update;
    }
}
