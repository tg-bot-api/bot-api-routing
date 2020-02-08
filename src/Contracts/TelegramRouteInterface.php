<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use Closure;

interface TelegramRouteInterface
{
    /**
     * @return RouteRuleInterface[]
     */
    public function getRules(): array;

    /**
     * @return Closure|string|null
     */
    public function getEndpoint();

    /**
     * @return ExtractorInterface[]
     */
    public function getExtractors(): array;

    /**
     * @return int
     */
    public function getWeight(): int;

    /**
     * @param                                 $fields
     * @param string|array|ExtractorInterface $extractor
     * @return TelegramRouteInterface
     * @todo !!important replace to other place
     */
    public function extract(array $fields, $extractor = null): TelegramRouteInterface;

    /**
     * @param RouterUpdateInterface $update
     * @return bool
     */
    public function match(RouterUpdateInterface $update): bool;
}
