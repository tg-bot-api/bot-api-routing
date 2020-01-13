<?php
declare(strict_types=1);

namespace App\TelegramRouter\Rules;

use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;

class DocumentSizeRule extends DocumentRule
{
    private $size;

    private $type;

    private $opertaors;

    public function __construct(int $size, string $type = '=')
    {
        $this->size = $size;
        $this->type = $type;

        $this->opertaors = [
            '=' => function ($var1, $var2) {
                return $var1 === $var2;
            },
            '>' => function ($var1, $var2) {
                return $var1 > $var2;
            },
            '<' => function ($var1, $var2) {
                return $var1 < $var2;
            },
            '<=' => function ($var1, $var2) {
                return $var1 <= $var2;
            },
            '>=' => function ($var1, $var2) {
                return $var1 >= $var2;
            },
            'not' => function ($var1, $var2) {
                return $var1 !== $var2;
            }
        ];
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!parent::match($update)) {
            return false;
        }

        return $this->opertaors[$this->type]($update->getUpdate()->message->document->fileSize, $this->size);
    }
}
