<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Interfaces;

interface UpdateTypeTypes
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
}
