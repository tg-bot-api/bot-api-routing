<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Contracts;

use TgBotApi\BotApiBase\Type\UpdateType;

interface RouterUpdateInterface
{
    public const  TYPE_EDITED_MESSAGE = 'editedMessage';
    public const  TYPE_MESSAGE = 'message';
    public const  TYPE_CALLBACK_QUERY = 'callbackQuery';
    public const  TYPE_CHANNEL_POST = 'channelPost';
    public const  TYPE_CHOSEN_INLINE_RESULT = 'chosenInlineResult';
    public const  TYPE_EDITED_CHANNEL_POST = 'editedChannelPost';
    public const  TYPE_INLINE_QUERY = 'inlineQuery';
    public const  TYPE_PRE_CHECKOUT_QUERY = 'preCheckoutQuery';
    public const  TYPE_SHIPPING_QUERY = 'shippingQuery';

    public const UPDATE_TYPES = [
        self::TYPE_EDITED_MESSAGE,
        self::TYPE_MESSAGE,
        self::TYPE_CALLBACK_QUERY,
        self::TYPE_CHANNEL_POST,
        self::TYPE_CHOSEN_INLINE_RESULT,
        self::TYPE_EDITED_CHANNEL_POST,
        self::TYPE_INLINE_QUERY,
        self::TYPE_PRE_CHECKOUT_QUERY,
        self::TYPE_SHIPPING_QUERY
    ];

    public function getType(): ?string;

    public function getUpdate(): UpdateType;

    public function getContext(): ContextInterface;

    public function getRoute(): TelegramRouteInterface;

    public function setRoute(TelegramRouteInterface $route): void;
}
