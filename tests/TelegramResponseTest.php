<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Method\GetMeMethod;
use TgBotApi\BotApiBase\Type\UserType;
use TgBotApi\BotApiRouting\Contracts\ContextInterface;

class TelegramResponseTest extends TestCase
{

    public function testGetTelegramMethod(): void
    {
        $response = new TelegramResponse(GetMeMethod::create());

        $this->assertInstanceOf(GetMeMethod::class, $response->getTelegramMethod());
    }

    public function testResolve(): void
    {
        $serverResponse = new UserType();
        $context = new Context();
        $context->set('id1', 'value1');
        $context->set('id2', 'value2');
        $response = new TelegramResponse(GetMeMethod::create());
        $response
            ->then(static function ($response, ContextInterface $context) use ($serverResponse) {
                Assert::assertEquals($response, $serverResponse);
                Assert::assertEquals($context->get('id1'), 'value1');
            })
            ->then(static function ($response, ContextInterface $context) use ($serverResponse) {
                Assert::assertEquals($response, $serverResponse);
                Assert::assertEquals($context->get('id2'), 'value2');
            });

        $response->resolve($serverResponse, $context);
    }
}
