<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Contracts\ContextInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;

/**
 * Class RouterUpdate
 *
 * @package App\TelegramRouter
 */
class RouterUpdate implements RouterUpdateInterface
{
    /**
     * @var UpdateType
     */
    private $update;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var TelegramRouteInterface
     */
    private $route;

    /**
     * RouterUpdate constructor.
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
     * @return ContextInterface
     */
    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    /**
     * @return TelegramRouteInterface
     */
    public function getActivatedRoute(): ?TelegramRouteInterface
    {
        return $this->route;
    }

    /**
     * @param TelegramRouteInterface $route
     */
    public function setActivatedRoute(TelegramRouteInterface $route): void
    {
        $this->route = $route;
    }
}
