<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Contracts\ContextInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;

/**
 * Class RouterUpdateType
 *
 * @package App\TelegramRouter
 */
class RouterUpdateType implements RouterUpdateInterface
{
    /**
     * @var UpdateType
     */
    private $update;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var TelegramRouteInterface
     */
    private $route;

    /**
     * RouterUpdateType constructor.
     *
     * @param UpdateType       $update
     * @param ContextInterface $context
     */
    public function __construct(UpdateType $update, ContextInterface $context)
    {
        $this->update = $update;
        $this->context = $context;
    }

    /**
     * @return UpdateType
     */
    public function getUpdate(): UpdateType
    {
        return $this->update;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        foreach (static::UPDATE_TYPES as $type) {
            if ($this->update->$type) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @return Context
     */
    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    /**
     * @return TelegramRouteInterface
     */
    public function getRoute(): TelegramRouteInterface
    {
        return $this->route;
    }

    /**
     * @param TelegramRouteInterface $route
     */
    public function setRoute(TelegramRouteInterface $route): void
    {
        $this->route = $route;
    }
}
