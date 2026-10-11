<?php

use Noo\CraftTwigHelpers\tags\MinifyTokenParser;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Twig\TwigFunction;

if (! class_exists('Craft', false)) {
    // Minimal stand-in for the Craft application, which the compiled tag reads devMode from.
    eval('class Craft { public static $app; }');
}

function render_minify(string $template, bool $useYield, bool $devMode = false): string
{
    Craft::$app = new class($devMode)
    {
        public function __construct(public bool $devMode) {}

        public function getConfig(): static
        {
            return $this;
        }

        public function getGeneral(): static
        {
            return $this;
        }
    };

    $options = method_exists(Environment::class, 'useYield') ? ['use_yield' => $useYield] : [];
    $env = new Environment(new ArrayLoader(['t' => $template]), $options);
    $env->addExtension(new class extends AbstractExtension
    {
        public function getTokenParsers(): array
        {
            return [new MinifyTokenParser];
        }

        public function getFunctions(): array
        {
            return [new TwigFunction('boom', fn () => throw new RuntimeException('boom'))];
        }
    });

    return $env->render('t');
}

it('minifies the wrapped output', function (bool $useYield) {
    $html = render_minify("{% minify %}<div>\n    <p>{{ 'Hi' }}</p>\n</div>{% endminify %}", $useYield);

    expect($html)->toBe('<div><p>Hi</p></div>');
})->with(['echo' => false, 'yield' => true]);

it('leaves output untouched in dev mode', function (bool $useYield) {
    $template = "{% minify %}<div>\n    <p>Hi</p>\n</div>{% endminify %}";

    expect(render_minify($template, $useYield, devMode: true))->toBe("<div>\n    <p>Hi</p>\n</div>");
})->with(['echo' => false, 'yield' => true]);

it('does not leak output buffers when the body throws', function (bool $useYield) {
    $level = ob_get_level();

    expect(fn () => render_minify('{% minify %}<p>{{ boom() }}</p>{% endminify %}', $useYield))
        ->toThrow(Twig\Error\RuntimeError::class);

    expect(ob_get_level())->toBe($level);
})->with(['echo' => false, 'yield' => true]);
