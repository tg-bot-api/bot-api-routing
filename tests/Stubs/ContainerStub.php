<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Stubs;

use Psr\Container\ContainerInterface;

class ContainerStub implements ContainerInterface
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->data[$id];
    }


    public function has($id): bool
    {
        return array_key_exists($id, $this->data);
    }
}
