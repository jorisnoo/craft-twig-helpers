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

it('keeps the whole original document when a protected block exceeds a regex limit', function () {
    $html = " \r\n<!-- document note --><script>const text = '".str_repeat('example ', 2000)."';</script>\r\n ";
    $previous = ini_set('pcre.backtrack_limit', '1000');
    try {
        expect(MinifyHtml::minify($html))->toBe($html);
    } finally {
        ini_set('pcre.backtrack_limit', $previous);
    }
});

it('protects literal markup and inline textarea spacing before removing ordinary comments', function () {
    $pre = "<pre>\r\n  <!-- code -->\r\n</pre>";
    $label = '<label>Note <textarea title="a > b">Use <!-- like this --> here</textarea> here</label>';
    $text = '<p>hello     world</p>';

    expect(MinifyHtml::minify($pre.'<!-- removed -->'.$label.$text))->toBe($pre.$label.$text);
});

it('preserves conditional comments and normalizes legacy script and style wrappers', function () {
    $conditional = '<!--[if IE]>message<![endif]-->';
    $script = "<script><!--\n const text = '<!-- example -->';\n// --></script>";
    $style = '<style><!-- p { color: red } --></style>';

    expect(MinifyHtml::minify($conditional.$script.$style))
        ->toBe($conditional."<script>const text = '<!-- example -->';</script><style>p { color: red }</style>");
});
