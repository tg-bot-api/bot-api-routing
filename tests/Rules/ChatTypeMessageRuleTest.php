<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class ChatTypeMessageRuleTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testMatchTrueOneType(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_CHANNEL]);
        $this->assertTrue($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchTrueManyTypes(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_GROUP, ChatType::TYPE_SUPERGROUP, ChatType::TYPE_CHANNEL]);
        $this->assertTrue($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchTrueOneTypeExclude(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_PRIVATE], true);
        $this->assertTrue($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchTrueManyTypesExclude(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_PRIVATE, ChatType::TYPE_GROUP], true);
        $this->assertTrue($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchFalseOneType(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_GROUP]);
        $this->assertFalse($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchFalseManyTypes(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_GROUP, ChatType::TYPE_SUPERGROUP, ChatType::TYPE_PRIVATE]);
        $this->assertFalse($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchFalseOneTypeExclude(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_CHANNEL], true);
        $this->assertFalse($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchFalseManyTypesExclude(): void
    {
        $rule = new ChatTypeRule([ChatType::TYPE_PRIVATE, ChatType::TYPE_CHANNEL], true);
        $this->assertFalse($rule->match($this->getUpdate(ChatType::TYPE_CHANNEL)));
    }

    public function testMatchFalseNoChat(): void
    {
        $update = $this->getRouterUpdate();
        $rule = new ChatTypeRule([ChatType::TYPE_PRIVATE, ChatType::TYPE_CHANNEL], true);
        $this->assertFalse($rule->match($update));
    }

    public function testMatchFalseNoChatTypeType(): void
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->chat = new ChatType();
        $rule = new ChatTypeRule([ChatType::TYPE_PRIVATE, ChatType::TYPE_CHANNEL], true);
        $this->assertFalse($rule->match($update));
    }

    private function getUpdate(string $type): RouterUpdateInterface
    {
        $update = $this->getRouterUpdate();
        $update->getUpdate()->message->chat = new ChatType();
        $update->getUpdate()->message->chat->type = $type;
        return $update;
    }
}
