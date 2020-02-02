<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts\DTO;

use PHPUnit\Framework\TestCase;
use TgBotApi\BotApiRouting\Rules\traits\GetRouterUpdateTrait;

class ParamRequestTest extends TestCase
{
    use GetRouterUpdateTrait;

    public function testCreateFromClosure(): void
    {
        $closure = static function () {
        };

        $update = $this->getRouterUpdate();
        $request = ParamRequest::createFromClosure($update, $closure);

        $this->assertEquals($update, $request->getUpdate());
        $this->assertEquals($closure, $request->getClosure());
        $this->assertNull($request->getMethodName());
        $this->assertNull($request->getClass());
    }

    public function testCreateFromClassAndMethod(): void
    {
        $class = new \stdClass();
        $update = $this->getRouterUpdate();
        $request = ParamRequest::createFromMethodAndClass($update, 'method', $class);

        $this->assertEquals($update, $request->getUpdate());
        $this->assertEquals($class, $request->getClass());
        $this->assertEquals('method', $request->getMethodName());
        $this->assertNull($request->getClosure());
    }
}
