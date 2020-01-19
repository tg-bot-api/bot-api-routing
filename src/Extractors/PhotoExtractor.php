<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractors;

use TgBotApi\BotApiBase\Type\PhotoSizeType;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class PhotoExtractor extends AbstractExtractor
{
    /**
     * @param RouterUpdateInterface $update
     * @param string                $key
     * @param                       $field
     * @return PhotoSizeType | PhotoSizeType[]
     * @throws RouteExtractionException
     */
    protected function extractField(RouterUpdateInterface $update, string $key, $field)
    {
        $photos = $update->getUpdate()->message->photo ?: [];

        if (!count($photos)) {
            throw new RouteExtractionException(sprintf(
                'Invalid photo Array: %s',
                json_encode($update->getUpdate()->message->photo)
            ));
        }

        if ($field === 'allSizes') {
            return $photos;
        }

        $direction = null;

        if (is_string($field) && $direction = ['minSize' => 1, 'maxSize' => -1][$field] ?? null) {
            usort($photos, static function (PhotoSizeType $a, PhotoSizeType $b) use ($direction) {
                return ($a->height + $a->width <=> $b->height + $b->width) * $direction;
            });
            return $photos[0];
        }

        if (is_callable($field)) {
            $photo = $field($photos);
            if (!$photo) {
                throw new RouteExtractionException('Lambda function must return PhotoSizeType class');
            }
            return $photo;
        }

        throw new RouteExtractionException(
            'Unsupported mode, please use `allSizes`, `maxSize`, `minSize`, or lambda function as field value.'
        );
    }
}
