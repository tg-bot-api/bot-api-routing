<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\RouterUpdate;

class StartCommandExtractorTest extends TestCase
{

    /**
     * @throws RouteExtractionException
     */
    public function testExtract(): void
    {
        $text = base64_encode('someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)');
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        $updateType->message->text = sprintf('/start %s', $text);
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new StartCommandExtractor();
        $extractor->extract($routerUpdate, [
            'data' => 'someString',
            'anotherData' => 'anotherString'
        ]);

        $this->assertEquals('someData', $context->get('data'));
        $this->assertEquals('anotherVeryLooongStringVeryVeryLooooong', $context->get('anotherData'));
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractNotStart(): void
    {
        $text = base64_encode('someString(someData)');
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        $updateType->message->text = sprintf('/notStart %s', $text);
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new StartCommandExtractor();
        $this->expectExceptionMessageMatches('/Command is not start/');
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, [
            'data' => 'someString',
        ]);
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractNotPattern(): void
    {
        $text = base64_encode('someString(someData)');
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        $updateType->message->text = sprintf('/start %s', $text);
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new StartCommandExtractor();
        $this->expectExceptionMessageMatches(
            '/Not found key`some` in `someString\(someData\)`. Please use `key\(value\)` format/'
        );
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, [
            'data' => 'some',
        ]);
    }
}
