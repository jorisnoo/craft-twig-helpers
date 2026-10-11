<?php

namespace Noo\CraftTwigHelpers;

use Noo\CraftTwigHelpers\config\TwigHelpersConfig;
use Noo\CraftTwigHelpers\filters\TrimEmptyParagraphsFilter;
use Noo\CraftTwigHelpers\functions\PlaceholderImageFunction;
use Noo\CraftTwigHelpers\tags\MinifyTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
use yii\base\InvalidConfigException;

class TwigHelpersExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly TwigHelpersConfig $config,
    ) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('trimEmptyParagraphs', new TrimEmptyParagraphsFilter, ['preserves_safety' => ['html']]),
            ...$this->configured($this->config->filters, 'Filters', fn (string $name, $fn) => new TwigFilter($name, $fn)),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('placeholderImage', new PlaceholderImageFunction),
            ...$this->configured($this->config->functions, 'Functions', fn (string $name, $fn) => new TwigFunction($name, $fn)),
        ];
    }

    public function getGlobals(): array
    {
        return $this->config->globals;
    }

    public function getTokenParsers(): array
    {
        return [
            new MinifyTokenParser,
        ];
    }

    public function getTests(): array
    {
        return $this->configured($this->config->tests, 'Tests', fn (string $name, $test) => new TwigTest($name, $test));
    }

    /**
     * Builds Twig callables from a config map, requiring named (non-numeric) keys.
     *
     * @template T
     *
     * @param  array<array-key, mixed>  $definitions
     * @param  callable(string, mixed): T  $factory
     * @return list<T>
     *
     * @throws InvalidConfigException
     */
    private function configured(array $definitions, string $kind, callable $factory): array
    {
        $items = [];

        foreach ($definitions as $name => $callable) {
            if (is_numeric($name)) {
                throw new InvalidConfigException("{$kind} must be declared with an alphanumeric name.");
            }

            $items[] = $factory((string) $name, $callable);
        }

        return $items;
    }
}
