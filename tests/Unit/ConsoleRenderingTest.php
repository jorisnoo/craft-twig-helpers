<?php

use craft\console\Application;
use craft\services\Config;
use craft\web\Application as WebApplication;
use craft\web\twig\Environment;
use craft\web\View;
use Noo\CraftTwigHelpers\TwigHelpers;
use Twig\Error\RuntimeError;
use Twig\Loader\ArrayLoader;

beforeAll(function () {
    $cms = dirname(__DIR__, 2).'/vendor/craftcms/cms';
    require_once $cms.'/lib/yii2/Yii.php';
    require_once $cms.'/src/Craft.php';
});

beforeEach(function () {
    // Exercise Craft's real module-init event, config loader and view registration
    // without booting unrelated services (database, installed plugins or queue).
    $this->previousApp = Craft::$app;
    Craft::$app = (new ReflectionClass(Application::class))->newInstanceWithoutConstructor();
    Craft::$app->set('config', new Config(['configDir' => dirname(__DIR__).'/fixtures/config']));
    Craft::$app->set('view', (new ReflectionClass(View::class))->newInstanceWithoutConstructor());
});

afterEach(function () {
    Craft::$app = $this->previousApp;
});

it('renders built-in and configured helpers after console initialization', function (bool $yield) {
    $template = "{% minify %}<div>\n  <p>{{ greet(salutation)|shout }} {{ (salutation is short) ? 'yes' : 'no' }}{{ field|trimEmptyParagraphs }}</p>\n</div>{% endminify %}";
    $twig = new Environment(new ArrayLoader(['main' => $template]), ['use_yield' => $yield]);
    $view = Craft::$app->getView();
    $view->setTwig($twig);
    new TwigHelpers('twig-helpers');
    Craft::$app->trigger(WebApplication::EVENT_INIT);
    $general = Craft::$app->getConfig()->getGeneral();

    $general->devMode = false;
    expect($view->getTwig()->render('main', ['field' => null]))->toBe('<div><p>HELLO HI yes</p></div>');
    $general->devMode = true;
    expect($view->getTwig()->render('main', ['field' => null]))->toBe("<div>\n  <p>HELLO HI yes</p>\n</div>");
    $template = $twig->createTemplate('{% minify %}{{ placeholderImage({}) }}{% endminify %}');
    $level = ob_get_level();
    expect(fn () => $template->render())->toThrow(RuntimeError::class)
        ->and(ob_get_level())->toBe($level);
})->with(['mixed output' => false, 'yield only' => true]);
