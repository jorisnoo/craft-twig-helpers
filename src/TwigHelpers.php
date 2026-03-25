<?php

namespace jorisnoo\CraftTwigHelpers;

use Craft;
use jorisnoo\CraftTwigHelpers\config\TwigHelpersConfig;
use jorisnoo\CraftTwigHelpers\twigextensions\TwigHelpersExtension;
use yii\base\Module;

class TwigHelpers extends Module
{
    public function init(): void
    {
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
