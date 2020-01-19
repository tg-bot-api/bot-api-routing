<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

interface ContainerWrapperInterface
{
    public function get(string $id);

    public function has(string $id);
}
