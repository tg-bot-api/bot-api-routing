<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use TgBotApi\BotApiRouting\Contracts\ExtractorInterface;
use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\Extractor\ArrayExtractor;

/**
 * Class TelegramRoute
 *
 * @package App\TelegramRouter
 */
class TelegramRoute implements TelegramRouteInterface
{
    /**
     * @var string
     */
    protected $updateType;

    /**
     * @var RouteRuleInterface[]
     */
    protected $rules;

    /**
     * @var callable|null
     */
    protected $endpoint;

    /**
     * @var ExtractorInterface[]
     */
    protected $extractors = [];

    /**
     * TelegramRoute constructor.
     *
     * @param string               $updateType
     * @param RouteRuleInterface[] $rules
     * @param string|callable      $endpoint
     */
    public function __construct(string $updateType, array $rules, $endpoint)
    {
        $this->updateType = $updateType;
        $this->rules = $rules;
        $this->endpoint = $endpoint;
    }

    /**
     * @return RouteRuleInterface[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return callable|string|null
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param array                           $fields
     * @param string|array|ExtractorInterface $extractor
     * @return TelegramRoute
     * @throws RouteExtractionException
     */
    public function extract(array $fields, $extractor = null): TelegramRouteInterface
    {
        if (!$extractor) {
            $this->extractors[] = [ArrayExtractor::class, $fields];
            return $this;
        }

        if ($extractor instanceof ExtractorInterface || is_string($extractor)) {
            $this->extractors[] = [$extractor, $fields];
            return $this;
        }

        if (is_object($extractor)) {
            throw new RouteExtractionException(sprintf(
                'Argument must be instance of %s or string className or array, %s provided.',
                ExtractorInterface::class,
                get_class($extractor)
            ));
        }

        throw new RouteExtractionException(sprintf(
            'Argument must be instance of %s or string className or array, %s provided.',
            ExtractorInterface::class,
            gettype($extractor)
        ));
    }

    /**
     * /**
     * @return string
     */
    public function getUpdateType(): string
    {
        return $this->updateType;
    }

    /**
     * @return array[ExtractorInterface, array]
     */
    public function getExtractors(): array
    {
        return $this->extractors;
    }

    /**
     * @param RouterUpdateInterface $update
     * @return bool
     */
    public function match(RouterUpdateInterface $update): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->match($update)) {
                return false;
            }
        }
        $update->setRoute($this);
        return true;
    }
}
