<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

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
