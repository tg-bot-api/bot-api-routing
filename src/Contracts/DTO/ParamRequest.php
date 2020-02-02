<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts\DTO;

use Closure;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class ParamRequest
{
    /**
     * @var Closure|null
     */
    private $closure;

    /**
     * @var string|null
     */
    private $methodName;

    /**
     * @var object|null
     */
    private $class;

    /**
     * @var RouterUpdateInterface
     */
    private $update;

    private function __construct()
    {
    }

    public static function createFromClosure(RouterUpdateInterface $update, Closure $closure): ParamRequest
    {
        $request = new static();
        $request->closure = $closure;
        $request->update = $update;

        return $request;
    }

    public static function createFromMethodAndClass(
        RouterUpdateInterface $update,
        string $methodName,
        object $class
    ): ParamRequest {
        $request = new static();
        $request->update = $update;
        $request->methodName = $methodName;
        $request->class = $class;

        return $request;
    }

    /**
     * @return Closure|null
     */
    public function getClosure(): ?Closure
    {
        return $this->closure;
    }

    /**
     * @return string|null
     */
    public function getMethodName(): ?string
    {
        return $this->methodName;
    }

    /**
     * @return object|null
     */
    public function getClass(): ?object
    {
        return $this->class;
    }

    /**
     * @return RouterUpdateInterface
     */
    public function getUpdate(): RouterUpdateInterface
    {
        return $this->update;
    }
}
