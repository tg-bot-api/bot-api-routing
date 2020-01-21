<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiRouting\Exceptions\RouteRuleException;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class ExtractableCommandRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    /**
     * @throws RouteRuleException
     */
    public function testMatchSuccess(): void
    {
        $text = base64_encode('someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)');
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = sprintf('/command %s', $text);
        $rule = new ExtractableCommandRule('command', ['someString', 'anotherString'], true);

        $this->assertTrue($rule->match($update));
    }

    /**
     * @throws RouteRuleException
     */
    public function testMatchSuccessWithoutOrder(): void
    {
        $text = base64_encode('someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)');
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = sprintf('/command %s', $text);
        $rule = new ExtractableCommandRule('command', ['anotherString', 'someString']);

        $this->assertTrue($rule->match($update));
    }

    /**
     * @throws RouteRuleException
     */
    public function testMatchWithoutVariables(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = '/command ';
        $rule = new ExtractableCommandRule('command', []);

        $this->assertTrue($rule->match($update));

        $update->getUpdate()->message->text = '/command any value';
        $this->assertTrue($rule->match($update));
    }

    /**
     * @throws RouteRuleException
     */
    public function testMatchNotcommand(): void
    {
        $text = base64_encode('someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)');
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = sprintf('/notCommand %s', $text);
        $rule = new ExtractableCommandRule('command', ['anotherString', 'someString']);

        $this->assertFalse($rule->match($update));
    }

    /**
     * @throws RouteRuleException
     */
    public function testMatchBadOrder(): void
    {
        $text = base64_encode('someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)');
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = sprintf('/command %s', $text);
        $rule = new ExtractableCommandRule('command', ['anotherString', 'someString'], true);

        $this->assertFalse($rule->match($update));
    }

    /**
     * @throws RouteRuleException
     */
    public function testMatchNoMessageText(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text;
        $rule = new ExtractableCommandRule('command', ['anotherString'], true);

        $this->assertFalse($rule->match($update));
    }

    /**
     * @throws RouteRuleException
     */
    public function testConstructorBadFormat(): void
    {
        $this->expectException(RouteRuleException::class);
        $this->expectExceptionMessageMatches(
            '/Variable name must match `.+?` \(camelCase\) but `BadString` provided/'
        );
        new ExtractableCommandRule('command', ['BadString']);
    }

    /**
     * @throws RouteRuleException
     */
    public function testConstructorBadType(): void
    {
        $this->expectException(RouteRuleException::class);
        $this->expectExceptionMessageMatches('/Variable 0 type should be string/');
        new ExtractableCommandRule('command', [null]);
    }
}
