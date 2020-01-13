<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

interface ContextInterface
{
    /**
     * @param string $id
     * @return mixed|null
     */
    public function get(string $id);

    /**
     * @param string $id
     * @param        $value
     * @return self
     */
    public function set(string $id, $value): self;

    /**
     * @param string $id
     * @return bool
     */
    public function isSet(string $id): bool;
}
