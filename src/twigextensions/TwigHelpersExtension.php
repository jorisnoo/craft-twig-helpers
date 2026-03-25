<?php

namespace Noo\CraftTwigHelpers\twigextensions;

use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\helpers\App;
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
            new TwigFunction('textSnippet', [$this, 'textSnippet']),
            new TwigFunction('staticAsset', [$this, 'staticAsset']),
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

    public function getTests(): array
    {
        $tests = [];

        foreach ($this->config->tests as $name => $test) {
            $tests[] = new TwigTest($name, $test);
        }

        return $tests;
    }

    public function hasTransparency(Asset $asset): bool
    {
        $localCopy = ImageTransforms::getLocalImageSource($asset);

        return Craft::$app->getImages()->loadImage($localCopy, true)->getIsTransparent() ?? false;
    }

    public function textSnippet(string $handle, string $sectionName = 'translations'): ?string
    {
        $translationsEntry = Entry::find()
            ->section($sectionName)
            ->with([$sectionName])
            ->one();

        return $translationsEntry?->getFieldValue($sectionName)[0][$handle] ?? null;
    }

    public function staticAsset(string $path): string
    {
        $cdnHost = App::env('ASSET_CDN_HOST');

        return $cdnHost
            ? $cdnHost . '/' . $path
            : Craft::getAlias('@web') . '/dist/' . $path;
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
