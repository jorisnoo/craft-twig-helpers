<?php

use Noo\CraftTwigHelpers\config\TwigHelpersConfig;

return TwigHelpersConfig::create()
    ->globals(['salutation' => 'Hi'])
    ->filters(['shout' => strtoupper(...)])
    ->functions(['greet' => fn (string $name) => "Hello $name"])
    ->tests(['short' => fn (string $value) => strlen($value) < 5]);
