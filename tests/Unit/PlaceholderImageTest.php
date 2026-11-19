<?php

use Noo\CraftTwigHelpers\functions\PlaceholderImageFunction;

it('encodes dimensions and color as well-formed SVG', function () {
    $color = "var(--paper, 'white')";
    $uri = (new PlaceholderImageFunction)(['width' => '800', 'height' => 1.5, 'color' => $color]);
    [$prefix, $encoded] = explode(',', $uri, 2);
    $doc = new DOMDocument;
    $doc->loadXML(rawurldecode($encoded));
    $svg = $doc->documentElement;

    expect($prefix)->toBe('data:image/svg+xml;charset=utf-8')
        ->and($svg->getAttribute('width'))->toBe('800')
        ->and($svg->getAttribute('height'))->toBe('1.5')
        ->and($svg->getAttribute('style'))->toBe('background:'.$color);
});

it('reports invalid placeholder parameters', function (array $config, string $parameter) {
    expect(fn () => (new PlaceholderImageFunction)($config))->toThrow(InvalidArgumentException::class, $parameter);
})->with([
    'missing width' => [['height' => 1], 'width'],
    'missing height' => [['width' => 1], 'height'],
    'zero' => [['width' => 0, 'height' => 1], 'width'],
    'not finite' => [['width' => INF, 'height' => 1], 'width'],
    'not numeric' => [['width' => 1, 'height' => 'auto'], 'height'],
    'invalid color type' => [['width' => 1, 'height' => 1, 'color' => []], 'color'],
]);
