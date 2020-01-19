<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class PhotoExtractor extends AbstractExtractor
{
    /**
     * @inheritDoc
     */
    protected function extractField(RouterUpdateInterface $update, string $key, $field)
    {
        if (is_callable($field)) {
            $photo = $field($update->getUpdate()->message->photo);
            if (!$photo) {
                throw new RouteExtractionException('Lambda function must be return value');
            }
            return $photo;
        }
        if ($field === 'all') {
            return $update->getUpdate()->message->photo;
        }

        $photos = $update->getUpdate()->message->photo;
        $otherTypes = ['min' => count($photos) - 1, 'max' => 0];

        if (isset($otherTypes[$field])) {
            return $photos[$otherTypes[$field]];
        }
        throw new RouteExtractionException(
            'Unsupported mode, please use "all", "max", "min", or lambda function as field value'
        );
    }
}
