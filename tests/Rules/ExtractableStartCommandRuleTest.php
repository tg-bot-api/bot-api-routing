<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiRouting\Exceptions\RouteRuleException;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class ExtractableStartCommandRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    /**
     * @throws RouteRuleException
     */
    public function testMatchSuccess(): void
    {
        $text = base64_encode('someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)');
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = sprintf('/start %s', $text);
        $rule = new ExtractableStartCommandRule(['someString', 'anotherString'], true);

        $this->assertTrue($rule->match($update));
    }

    /**
     * @throws RouteRuleException
     */
    public function testMatchFailed(): void
    {
        $text = base64_encode('someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)');
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->text = sprintf('/notStart %s', $text);
        $rule = new ExtractableStartCommandRule(['anotherString', 'someString']);

        $this->assertFalse($rule->match($update));
    }

}
