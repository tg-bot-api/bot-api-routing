<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Extractor;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;

class PhotoExtractor extends AbstractExtractor
{
    /**
     * this extractor is example, and it can not usable in project
     *
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     * @throws RouteExtractionException
     */
    public function extract(RouterUpdateInterface $update, array $fields): void
    {
        foreach ($fields as $key => $field) {
            $this->checkContextAvailability($update->getContext(), $key);
            if (is_callable($field)) {
                $photo = $field($update->getUpdate()->message->photo);
                if (!$photo) {
                    throw new RouteExtractionException('Lambda function must be return value');
                }
                $update->getContext()->set($key, $photo);
                continue;
            }
            if ($field === 'all') {
                $update->getContext()->set($key, $update->getUpdate()->message->photo);
                continue;
            }

            $photos = $update->getUpdate()->message->photo;
            $otherTypes = ['min' => count($photos) - 1, 'max' => 0];

            if (isset($otherTypes[$field])) {
                $update->getContext()->set($key, $photos[$otherTypes[$field]]);
                continue;
            }
            throw new RouteExtractionException('Unsupported mode, please use "all", "max", "min", or lambda function as field value');
        }
    }
}
