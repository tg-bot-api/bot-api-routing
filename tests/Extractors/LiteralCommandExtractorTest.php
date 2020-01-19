<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\RouterUpdate;

class LiteralCommandExtractorTest extends TestCase
{
    public function testExtract()
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        $updateType->message->text = sprintf('/command %s', 'This is command data');
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new LiteralCommandExtractor();
        $extractor->extract($routerUpdate, [
            'data' => 'command'
        ]);

        $this->assertEquals('This is command data', $context->get('data'));
    }

    public function testExtractException()
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        $updateType->message->text = '/command This is command data';
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new LiteralCommandExtractor();
        $this->expectException(RouteExtractionException::class);
        $this->expectExceptionMessageMatches(
            '/Command `notCommand` not found in message `\/command This is command data`/'
        );
        $extractor->extract($routerUpdate, [
            'data' => 'notCommand'
        ]);
    }
}
