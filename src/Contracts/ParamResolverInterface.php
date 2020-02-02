<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use TgBotApi\BotApiRouting\Contracts\DTO\ParamRequest;
use Traversable;

interface ParamResolverInterface
{
    /**
     * @param ParamRequest $request
     * @return Traversable|array
     */
    public function resolve(ParamRequest $request);
}
