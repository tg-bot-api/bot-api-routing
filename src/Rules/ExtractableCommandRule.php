<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting\Rules;

use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteRuleException;

class ExtractableCommandRule implements RouteRuleInterface
{
    private $variables;

    private $command;

    /**
     * ExtractableStartCommandRule constructor.
     *
     * @param string   $command
     * @param string[] $variables
     * @param bool     $keepOrder
     * @throws RouteRuleException
     */
    public function __construct(string $command, array $variables = [], bool $keepOrder = false)
    {
        $this->testVariables($variables);
        if (!$keepOrder) {
            $this->variables = array_map(static function ($variable) {
                return sprintf('/%s\((.*?)\)/', $variable);
            }, $variables);
        } else {
            $pattern = array_reduce($variables, static function ($variable, $item) {
                return sprintf('%s%s\((.*?)\)', $variable, $item);
            });
            $this->variables = [sprintf('/^%s$/', $pattern)];
        }

        $this->command = $command;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return mixed
     */
    public function match(RouterUpdateInterface $update): bool
    {
        if (!($text = $update->getUpdate()->message->text)) {
            return false;
        }

        if (!count($this->variables)) {
            return (bool)preg_match(sprintf('~^(/%s .*)|(/%1$s)$~', $this->command), $text);
        }

        $regex = sprintf(
            '~^/%s ((?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{4}))$~',
            $this->command
        );

        $encodedDataArray = [];

        if (!preg_match($regex, $text, $encodedDataArray) || count($encodedDataArray) !== 2) {
            return false;
        }

        $value = base64_decode(end($encodedDataArray));

        foreach ($this->variables as $pattern) {
            if (!preg_match($pattern, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $variables
     * @throws RouteRuleException
     */
    private function testVariables(array $variables): void
    {
        foreach ($variables as $index => $variable) {
            if (!is_string($variable)) {
                throw new RouteRuleException(sprintf('Variable %s type should be string', $index));
            }
            if (!preg_match('/^[a-z][a-zA-Z0-9]*$/', $variable)) {
                throw new RouteRuleException(sprintf(
                    'Variable name must match `^[a-z][a-zA-Z0-9]*$` (camelCase) but `%s` provided',
                    $variable
                ));
            }
        }
    }
}
