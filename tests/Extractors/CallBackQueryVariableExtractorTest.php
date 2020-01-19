<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\CallbackQueryType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\RouterUpdate;

class CallBackQueryVariableExtractorTest extends TestCase
{
    /**
     * @throws RouteExtractionException
     */
    public function testExtract(): void
    {
        $text = 'someString(someData)anotherString(anotherVeryLooongStringVeryVeryLooooong)';
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->callbackQuery = new CallbackQueryType();
        $updateType->callbackQuery->data = $text;
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new CallBackQueryVariableExtractor();
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
    public function testExtractNotPattern(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->callbackQuery = new CallbackQueryType();
        $updateType->callbackQuery->data = 'someString(someData)';
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new CallBackQueryVariableExtractor();
        $this->expectExceptionMessageMatches(
            '/Not found key`some` in `someString\(someData\)`. Please use `key\(value\)` format/'
        );
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, [
            'data' => 'some',
        ]);
    }

}
