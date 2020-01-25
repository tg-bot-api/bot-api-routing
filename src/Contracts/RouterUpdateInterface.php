<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use TgBotApi\BotApiBase\Type\UpdateType;
use TgBotApi\BotApiRouting\Interfaces\UpdateTypeTypes;

interface RouterUpdateInterface
{
    public const UPDATE_TYPES = [
        UpdateTypeTypes::TYPE_EDITED_MESSAGE,
        UpdateTypeTypes::TYPE_MESSAGE,
        UpdateTypeTypes::TYPE_CALLBACK_QUERY,
        UpdateTypeTypes::TYPE_CHANNEL_POST,
        UpdateTypeTypes::TYPE_CHOSEN_INLINE_RESULT,
        UpdateTypeTypes::TYPE_EDITED_CHANNEL_POST,
        UpdateTypeTypes::TYPE_INLINE_QUERY,
        UpdateTypeTypes::TYPE_PRE_CHECKOUT_QUERY,
        UpdateTypeTypes::TYPE_SHIPPING_QUERY
    ];

    public function getType(): ?string;

    public function getUpdate(): UpdateType;

    public function getContext(): ContextInterface;

    public function getRoute(): TelegramRouteInterface;

    public function setRoute(TelegramRouteInterface $route): void;
}
