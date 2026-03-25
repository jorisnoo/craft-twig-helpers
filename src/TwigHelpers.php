<?php

namespace Noo\CraftTwigHelpers;

use Craft;
use Noo\CraftTwigHelpers\config\TwigHelpersConfig;
use Noo\CraftTwigHelpers\twigextensions\TwigHelpersExtension;
use yii\base\Module;

class TwigHelpers extends Module
{
    public function init(): void
    {
        Craft::setAlias('@Noo/CraftTwigHelpers', __DIR__);

        parent::init();

        Craft::$app->onInit(function () {
            if (Craft::$app instanceof \craft\web\Application) {
                $configData = Craft::$app->config->getConfigFromFile('twig-helpers');
                $config = new TwigHelpersConfig($configData ?: []);

                Craft::$app->getView()->registerTwigExtension(
                    new TwigHelpersExtension($config)
                );
            }
        });
    }
}
