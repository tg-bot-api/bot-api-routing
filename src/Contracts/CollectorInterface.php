<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;


interface CollectorInterface
{

    public function collect(): void;
}
