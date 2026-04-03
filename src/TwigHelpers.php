<?php

namespace Noo\CraftTwigHelpers;

use Craft;
use craft\web\Application;
use Noo\CraftTwigHelpers\config\TwigHelpersConfig;
use yii\base\Module;

class TwigHelpers extends Module
{
    public function init(): void
    {
        Craft::setAlias('@Noo/CraftTwigHelpers', __DIR__);

        parent::init();

        Craft::$app->onInit(function () {
            if (Craft::$app instanceof Application) {
                $config = Craft::$app->config->getConfigFromFile('twig-helpers');

                if (! $config instanceof TwigHelpersConfig) {
                    $config = new TwigHelpersConfig($config ?: []);
                }

                Craft::$app->getView()->registerTwigExtension(
                    new TwigHelpersExtension($config)
                );
            }
        });
    }
}
