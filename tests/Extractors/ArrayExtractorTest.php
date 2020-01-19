<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\ChatType;
use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\RouterUpdate;

class ArrayExtractorTest extends TestCase
{

    /**
     * @throws RouteExtractionException
     */
    public function testExtractField(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        $updateType->updateId = '00001';
        $updateType->message->chat = new ChatType();
        $updateType->inlineQuery = ['stub' => 'arrayStub'];
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new ArrayExtractor();
        $extractor->extract($routerUpdate, [
            'message' => 'message',
            'updateId' => 'updateId',
            'chat' => 'message.chat',
            'arrayStub' => 'inlineQuery.stub'
        ]);

        $this->assertInstanceOf(MessageType::class, $context->get('message'));
        $this->assertInstanceOf(ChatType::class, $context->get('chat'));
        $this->assertEquals('00001', $context->get('updateId'));
        $this->assertEquals('arrayStub', $context->get('arrayStub'));
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractionNullError(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = null;
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new ArrayExtractor();
        $this->expectExceptionMessageMatches('/`a` partial of `message.a` is null or not defined/');
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, [
            'message' => 'message.a'
        ]);
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractionNotSupportedTypeError(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = 1;
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new ArrayExtractor();
        $this->expectExceptionMessageMatches('/Cannot access to `a` key on integer type/');
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, [
            'message' => 'message.a'
        ]);
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractionArrayUndefinedIndexError(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = [];
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new ArrayExtractor();
        $this->expectExceptionMessageMatches('/Cannot access to property `a` of array/');
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, [
            'message' => 'message.a'
        ]);
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractionObjectUndefinedIndexError(): void
    {
        $context = new Context();
        $updateType = new UpdateType();
        $updateType->message = new \stdClass();
        $routerUpdate = new RouterUpdate($updateType, $context);
        $extractor = new ArrayExtractor();
        $this->expectExceptionMessageMatches('/Cannot access to property `a` of stdClass/');
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, [
            'message' => 'message.a'
        ]);
    }
}
