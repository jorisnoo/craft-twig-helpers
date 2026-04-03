<?php

namespace Noo\CraftTwigHelpers\filters;

use Craft;
use craft\elements\Asset;
use craft\helpers\ImageTransforms;

class HasTransparencyFilter
{
    public function __invoke(Asset $asset): bool
    {
        $cacheKey = "hasTransparency:{$asset->id}:{$asset->dateModified->getTimestamp()}";

        return Craft::$app->getCache()->getOrSet($cacheKey, function () use ($asset) {
            $localCopy = ImageTransforms::getLocalImageSource($asset);

            return Craft::$app->getImages()->loadImage($localCopy, true)->getIsTransparent() ?? false;
        });
    }
}
