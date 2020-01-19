<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiRouting\Contracts\ContextInterface;

class Context implements ContextInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $id
     * @return mixed|null
     */
    public function get(string $id)
    {
        return $this->data[$id] ?? null;
    }

    /**
     * @param string $id
     * @param        $value
     * @return self
     */
    public function set(string $id, $value): ContextInterface
    {
        $this->data[$id] = $value;
        return $this;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function isSet(string $id): bool
    {
        return array_key_exists($id, $this->data);
    }
}
