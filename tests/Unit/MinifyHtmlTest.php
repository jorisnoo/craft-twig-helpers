<?php

use Noo\CraftTwigHelpers\MinifyHtml;

it('preserves html when unicode regexes encounter invalid utf-8', function () {
    $html = "<div>Valid content \xFF followed by more content</div>";

    expect(MinifyHtml::minify($html))->toBe($html);
});

it('preserves html when the pcre backtrack limit is exhausted', function () {
    $html = '<script>'.str_repeat('const value = 1; ', 100).'</script>';
    $backtrackLimit = ini_get('pcre.backtrack_limit');

    try {
        ini_set('pcre.backtrack_limit', '1');

        expect(MinifyHtml::minify($html))->toBe($html);
    } finally {
        ini_set('pcre.backtrack_limit', $backtrackLimit);
    }
});
