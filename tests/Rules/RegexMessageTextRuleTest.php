<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class RegexMessageTextRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatch(): void
    {
        $rule = new RegexMessageTextRule('/^[a-z A-Z]+$/');
        $update = $this->getRouterUpdate();

        $this->assertFalse($rule->match($update));

        $update->getUpdate()->message->text = 'matchedvalue';
        $this->assertTrue($rule->match($update));

        $update->getUpdate()->message->text = 'not-matchedvalue';
        $this->assertFalse($rule->match($update));
    }
}
