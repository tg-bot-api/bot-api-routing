<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiBase\Type\MessageType;
use TgBotApi\BotApiBase\Type\PhotoSizeType;
use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Context;
use TgBotApi\BotApiRouting\Contracts\ContextInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\RouterUpdate;

class PhotoExtractorTest extends TestCase
{

    /**
     * @throws RouteExtractionException
     */
    public function testExtractWithLambda(): void
    {
        $context = new Context();

        $routerUpdate = $this->createValidRouterUpdate($context, [4, 5, 6]);
        $extractor = new PhotoExtractor();
        $extractor->extract($routerUpdate, [
            'photo' => static function ($photos) {
                return $photos[1];
            }
        ]);

        $this->assertEquals($routerUpdate->getUpdate()->message->photo[1], $context->get('photo'));
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractWithLambdaError(): void
    {
        $routerUpdate = $this->createValidRouterUpdate(new Context());
        $extractor = new PhotoExtractor();
        $this->expectException(RouteExtractionException::class);
        $this->expectExceptionMessageMatches('/Lambda function must return PhotoSizeType class/');
        $extractor->extract($routerUpdate, [
            'photo' => static function ($photos) {
            }
        ]);
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractAll(): void
    {
        $context = new Context();
        $routerUpdate = $this->createValidRouterUpdate($context, [80, 20, 40, 100]);
        $extractor = new PhotoExtractor();

        $extractor->extract($routerUpdate, [
            'photo' => 'allSizes',
            'photoMin' => 'minSize',
            'photoMax' => 'maxSize'
        ]);

        $this->assertEquals($routerUpdate->getUpdate()->message->photo, $context->get('photo'));
        $this->assertEquals($routerUpdate->getUpdate()->message->photo[1], $context->get('photoMin'));
        $this->assertEquals($routerUpdate->getUpdate()->message->photo[3], $context->get('photoMax'));
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractNoPhotoFound(): void
    {
        $context = new Context();
        $routerUpdate = $this->createValidRouterUpdate($context, []);
        $extractor = new PhotoExtractor();
        $this->expectException(RouteExtractionException::class);
        $this->expectExceptionMessageMatches('/Invalid photo Array: null/');
        $extractor->extract($routerUpdate, [
            'photo' => 'allSizes',
            'photoMin' => 'minSize',
            'photoMax' => 'maxSize'
        ]);
    }

    /**
     * @throws RouteExtractionException
     */
    public function testExtractNotValidInput(): void
    {
        $context = new Context();
        $routerUpdate = $this->createValidRouterUpdate($context);
        $extractor = new PhotoExtractor();
        $this->expectException(RouteExtractionException::class);
        $this->expectExceptionMessageMatches(
            '/Unsupported mode, please use `allSizes`, `maxSize`, `minSize`, or lambda function as field value./'
        );
        $extractor->extract($routerUpdate, [
            'photo' => 'notValidInput',
        ]);
    }

    /**
     * @param ContextInterface $context
     * @param array|null       $size
     * @return RouterUpdateInterface
     */
    private function createValidRouterUpdate(
        ContextInterface $context,
        array $size = [20, 30, 40]
    ): RouterUpdateInterface {
        $updateType = new UpdateType();
        $updateType->message = new MessageType();
        $updateType->message->photo = count($size) ? [] : null;
        foreach ($size as $s) {
            $updateType->message->photo[] = $this->createPhoto($s, $s, -$s);
        }

        return new RouterUpdate($updateType, $context);
    }

    /**
     * @param int    $height
     * @param int    $width
     * @param string $id
     * @return PhotoSizeType
     */
    private function createPhoto(int $height, int $width, $id = 'id'): PhotoSizeType
    {
        $photo = new PhotoSizeType();
        $photo->height = $height;
        $photo->width = $width;
        $photo->fileId = $id;
        return $photo;
    }
}
