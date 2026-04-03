<?php

namespace Noo\CraftTwigHelpers;

use Noo\CraftTwigHelpers\config\TwigHelpersConfig;
use Noo\CraftTwigHelpers\filters\HasTransparencyFilter;
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
        $filters = [
            new TwigFilter('hasTransparency', new HasTransparencyFilter),
            new TwigFilter('trimEmptyParagraphs', new TrimEmptyParagraphsFilter, ['is_safe' => ['html']]),
        ];

        foreach ($this->config->filters as $name => $fn) {
            if (is_numeric($name)) {
                throw new InvalidConfigException('Filters must be declared with an alphanumeric name.');
            }

            $filters[] = new TwigFilter($name, $fn);
        }

        return $filters;
    }

    public function getFunctions(): array
    {
        $functions = [
            new TwigFunction('placeholderImage', new PlaceholderImageFunction),
        ];

        foreach ($this->config->functions as $name => $fn) {
            if (is_numeric($name)) {
                throw new InvalidConfigException('Functions must be declared with an alphanumeric name.');
            }

            $functions[] = new TwigFunction($name, $fn);
        }

        return $functions;
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
        $tests = [];

        foreach ($this->config->tests as $name => $test) {
            $tests[] = new TwigTest($name, $test);
        }

        return $tests;
    }
}
