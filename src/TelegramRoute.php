<?php
declare(strict_types=1);

namespace TgBotApi\BotApiRouting;

use Closure;
use TgBotApi\BotApiRouting\Contracts\ExtractorInterface;
use TgBotApi\BotApiRouting\Contracts\RouteRuleInterface;
use TgBotApi\BotApiRouting\Contracts\RouterUpdateInterface;
use TgBotApi\BotApiRouting\Contracts\TelegramRouteInterface;
use TgBotApi\BotApiRouting\Exceptions\RouteExtractionException;
use TgBotApi\BotApiRouting\Extractors\ArrayExtractor;

/**
 * Class TelegramRoute
 *
 * @package App\TelegramRouter
 */
class TelegramRoute implements TelegramRouteInterface
{
    /**
     * @var RouteRuleInterface[]
     */
    protected $rules;

    /**
     * @var callable|string|null
     */
    protected $endpoint;

    /**
     * @var ExtractorInterface[]
     */
    protected $extractors = [];

    /**
     * @var int
     */
    protected $weight;

    /**
     * TelegramRoute constructor.
     *
     * @param RouteRuleInterface[] $rules
     * @param string|callable      $endpoint
     * @param int|null             $weight
     */
    public function __construct(array $rules, $endpoint, int $weight = 0)
    {
        $this->weight = $weight;
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
     * @return Closure|string|null
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param array                           $fields
     * @param string|array|ExtractorInterface $extractor
     * @return TelegramRouteInterface
     * @throws RouteExtractionException
     */
    public function extract(array $fields, $extractor = null): TelegramRouteInterface
    {
        if ($extractor === null) {
            $this->extractors[] = [ArrayExtractor::class, $fields];
            return $this;
        }

        if (is_string($extractor) && !class_exists($extractor)) {
            throw new RouteExtractionException(sprintf(
                '$extractor should be valid class path `%s` provided.',
                $extractor
            ));
        }


        if (is_string($extractor) && !in_array(ExtractorInterface::class, class_implements($extractor), true)) {
            throw new RouteExtractionException(sprintf(
                '$extractor must implements `%s`, `%s` provided',
                ExtractorInterface::class,
                $extractor
            ));
        }

        if ($extractor instanceof ExtractorInterface || is_string($extractor)) {
            $this->extractors[] = [$extractor, $fields];
            return $this;
        }

        if (is_object($extractor)) {
            throw new RouteExtractionException(sprintf(
                'Argument must be instance of `%s` or string className, or null, `%s` provided.',
                ExtractorInterface::class,
                get_class($extractor)
            ));
        }

        throw new RouteExtractionException(sprintf(
            'Argument must be instance of `%s` or string className or null, `%s` provided.',
            ExtractorInterface::class,
            gettype($extractor)
        ));
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
        $update->setActivatedRoute($this);
        return true;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }
}
