<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionEmptyException;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\Extractor\AbstractExtractor;
use TgBotApi\BotApiRouting\RouterUpdate;

class AbstractExtractorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @throws RouteExtractionException
     * @throws \ReflectionException
     */
    public function testExtractEmptyError(): void
    {
        $routerUpdate = new RouterUpdate(new UpdateType(), new Context());
        $extractor = $this->getExtractorStub();
        $extractor->method('extractField')->willThrowException(new RouteExtractionEmptyException());
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate);
    }

    /**
     * @throws RouteExtractionException
     * @throws \ReflectionException
     */
    public function testExtractEmptyNoError(): void
    {
        $context = new Context();
        $routerUpdate = new RouterUpdate(new UpdateType(), $context);
        $extractor = $this->getExtractorStub(true);
        $extractor->expects(self::at(0))
            ->method('extractField')
            ->willThrowException(new RouteExtractionEmptyException());

        $extractor->method('extractField')
            ->willReturn('i');

        $extractor->extract($routerUpdate, ['r' => 'g', 'y' => 'i']);
        $this->assertEquals('i', $context->get('y'));
    }

    /**
     * @throws RouteExtractionException
     * @throws \ReflectionException
     */
    public function testExtractNormal(): void
    {
        $context = new Context();
        $routerUpdate = new RouterUpdate(new UpdateType(), $context);
        $extractor = $this->getExtractorStub();
        $extractor->method('extractField')->willReturnCallback(
            static function (RouterUpdate $update, string $key, $field) {
                return $key . $field;
            }
        );
        $extractor->extract($routerUpdate, ['r' => 'g', 'y' => 'i']);
        $this->assertEquals('rg', $context->get('r'));
        $this->assertEquals('yi', $context->get('y'));
    }

    /**
     * @throws RouteExtractionException
     * @throws \ReflectionException
     */
    public function testExtractContextUnavailable(): void
    {
        $context = new Context();
        $context->set('a', null);
        $routerUpdate = new RouterUpdate(new UpdateType(), $context);
        $extractor = $this->getExtractorStub();
        $extractor->method('extractField')->willReturn('a');
        $this->expectException(RouteExtractionException::class);
        $extractor->extract($routerUpdate, ['a' => 'g']);
    }


    /**
     * @param $ignoreEmptyExtraction
     * @return AbstractExtractor|MockObject
     * @throws \ReflectionException
     */
    public function getExtractorStub($ignoreEmptyExtraction = false): AbstractExtractor
    {
        $extractor = $this->getMockForAbstractClass(AbstractExtractor::class);
        $reflection = new ReflectionClass($extractor);
        $reflectionProperty = $reflection->getProperty('ignoreEmptyExtraction');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($extractor, $ignoreEmptyExtraction);
        return $extractor;
    }
}
