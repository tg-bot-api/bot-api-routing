<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use TgBotApi\BotApiBase\Type\UpdateType;

interface RouterUpdateInterface
{
    public const UPDATE_TYPES = [
        RouteSetterInterface::TYPE_EDITED_MESSAGE,
        RouteSetterInterface::TYPE_MESSAGE,
        RouteSetterInterface::TYPE_CALLBACK_QUERY,
        RouteSetterInterface::TYPE_CHANNEL_POST,
        RouteSetterInterface::TYPE_CHOSEN_INLINE_RESULT,
        RouteSetterInterface::TYPE_EDITED_CHANNEL_POST,
        RouteSetterInterface::TYPE_INLINE_QUERY,
        RouteSetterInterface::TYPE_PRE_CHECKOUT_QUERY,
        RouteSetterInterface::TYPE_SHIPPING_QUERY
    ];

    public function getType(): ?string;

    public function getUpdate(): UpdateType;

    public function getContext(): ContextInterface;

    public function getRoute(): TelegramRouteInterface;

    public function setRoute(TelegramRouteInterface $route): void;
}
