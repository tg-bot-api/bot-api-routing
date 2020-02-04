<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use Psr\Container\ContainerInterface;

interface ContextInterface extends ContainerInterface
{
    /**
     * @param string $id
     * @param        $value
     * @return self
     */
    public function set(string $id, $value): ContextInterface;
}
