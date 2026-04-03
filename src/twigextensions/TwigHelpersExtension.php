<?php

namespace Noo\CraftTwigHelpers\twigextensions;

use Craft;
use craft\elements\Asset;
use craft\helpers\ImageTransforms;
use Noo\CraftTwigHelpers\config\TwigHelpersConfig;
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
            new TwigFilter('hasTransparency', [$this, 'hasTransparency']),
            new TwigFilter('trimEmptyParagraphs', [$this, 'trimEmptyParagraphs'], ['is_safe' => ['html']]),
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
            new TwigFunction('placeholderImage', [$this, 'placeholderImage']),
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
            new MinifyTokenParser(),
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

    public function trimEmptyParagraphs(string $html): string
    {
        $emptyP = '<p(?:\s[^>]*)?>(?:\s|\x{00A0}|&nbsp;|<br[^>]*\/?>)*<\/p>';

        $html = preg_replace('~^(\s*' . $emptyP . '\s*)+~isu', '', $html);
        $html = preg_replace('~(\s*' . $emptyP . '\s*)+$~isu', '', $html);

        return trim($html);
    }

    public function hasTransparency(Asset $asset): bool
    {
        $localCopy = ImageTransforms::getLocalImageSource($asset);

        return Craft::$app->getImages()->loadImage($localCopy, true)->getIsTransparent() ?? false;
    }

    public function placeholderImage(array $config): string
    {
        $width = $config['width'];
        $height = $config['height'];
        $color = $config['color'] ?? 'transparent';

        return 'data:image/svg+xml;charset=utf-8,' . rawurlencode(
            sprintf(
                '<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'%s\' height=\'%s\' style=\'background:%s\'/>',
                $width,
                $height,
                $color,
            )
        );
    }
}
