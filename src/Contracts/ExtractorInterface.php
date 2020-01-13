<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\TelegramRouter\Extractor;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

/**
 * Interface ExtractorInterface
 *
 * @package App\TelegramRouter\Extractor
 */
interface ExtractorInterface
{
    /**
     * @param RouterUpdateInterface $update
     * @param array                 $fields
     * @return void
     */
    public function extract(RouterUpdateInterface $update, array $fields): void;
}
