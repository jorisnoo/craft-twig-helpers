<?php

use Noo\CraftTwigHelpers\filters\TrimEmptyParagraphsFilter;

function trim_p(string $html): string
{
    return (new TrimEmptyParagraphsFilter)($html);
}

it('removes empty paragraphs from the beginning', function (string $input, string $expected) {
    expect(trim_p($input))->toBe($expected);
})->with([
    'empty <p></p>' => ['<p></p><p>Content</p>', '<p>Content</p>'],
    'empty <p><br></p>' => ['<p><br></p><p>Content</p>', '<p>Content</p>'],
    'empty <p><br/></p>' => ['<p><br/></p><p>Content</p>', '<p>Content</p>'],
    'empty <p><br /></p>' => ['<p><br /></p><p>Content</p>', '<p>Content</p>'],
    'empty <p>&nbsp;</p>' => ['<p>&nbsp;</p><p>Content</p>', '<p>Content</p>'],
    'empty <p> </p>' => ['<p> </p><p>Content</p>', '<p>Content</p>'],
    'multiple empty paragraphs' => ['<p></p><p><br></p><p>&nbsp;</p><p>Content</p>', '<p>Content</p>'],
]);

it('removes empty paragraphs from the end', function (string $input, string $expected) {
    expect(trim_p($input))->toBe($expected);
})->with([
    'empty <p></p>' => ['<p>Content</p><p></p>', '<p>Content</p>'],
    'empty <p><br></p>' => ['<p>Content</p><p><br></p>', '<p>Content</p>'],
    'empty <p>&nbsp;</p>' => ['<p>Content</p><p>&nbsp;</p>', '<p>Content</p>'],
    'multiple empty paragraphs' => ['<p>Content</p><p></p><p><br></p><p>&nbsp;</p>', '<p>Content</p>'],
]);

it('removes empty paragraphs from both sides', function () {
    $input = '<p></p><p><br></p><p>Content</p><p>&nbsp;</p><p></p>';
    expect(trim_p($input))->toBe('<p>Content</p>');
});

it('preserves empty paragraphs in the middle', function () {
    $input = '<p>Start</p><p></p><p>End</p>';
    expect(trim_p($input))->toBe($input);
});

it('preserves content-only html unchanged', function () {
    $input = '<p>Just some content</p>';
    expect(trim_p($input))->toBe($input);
});

it('returns empty string when all paragraphs are empty', function () {
    $input = '<p></p><p><br></p><p>&nbsp;</p>';
    expect(trim_p($input))->toBe('');
});

it('handles paragraphs with attributes', function () {
    $input = '<p class="lead"></p><p>Content</p><p style="margin:0"></p>';
    expect(trim_p($input))->toBe('<p>Content</p>');
});

it('handles whitespace between tags', function () {
    $input = "<p></p>\n<p><br></p>\n<p>Content</p>\n<p></p>";
    expect(trim_p($input))->toBe('<p>Content</p>');
});

it('handles br tags with attributes (cke-filler)', function () {
    $input = '<p>Baukonsortium Lindenpark Uzwil</p><p><br data-cke-filler="true"></p>';
    expect(trim_p($input))->toBe('<p>Baukonsortium Lindenpark Uzwil</p>');
});

it('handles unicode non-breaking space character', function () {
    $input = "<p>Baukonsortium Lindenpark Uzwil</p><p>\xC2\xA0</p>";
    expect(trim_p($input))->toBe('<p>Baukonsortium Lindenpark Uzwil</p>');
});

it('returns Markup when given Markup, so safety survives auto-escaping', function () {
    $input = new Twig\Markup('<p>Content</p><p>&nbsp;</p>', 'UTF-8');
    $result = (new TrimEmptyParagraphsFilter)($input);
    expect($result)->toBeInstanceOf(Twig\Markup::class)
        ->and((string) $result)->toBe('<p>Content</p>');
});

it('returns a plain string when given a plain string', function () {
    expect((new TrimEmptyParagraphsFilter)('<p>Content</p>'))->toBeString();
});
