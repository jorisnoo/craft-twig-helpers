<?php

/**
 * HTML Minifier
 *
 * Regex-based removal of whitespace, unnecessary comments and tokens.
 * IE conditional comments are preserved. Inline <script> and <style>
 * blocks are preserved (trimmed, not minified).
 *
 * Adapted from Minify_HTML by Stephen Clay (BSD-3-Clause)
 *
 * @see https://github.com/mrclay/minify
 */

namespace Noo\CraftTwigHelpers;

use Craft;
use RuntimeException;

class MinifyHtml
{
    public function __construct(private readonly string $html) {}

    public static function minify(string $html): string
    {
        return (new self($html))->process();
    }

    public function process(): string
    {
        $prefix = '%MINIFYHTML:';
        while (str_contains($this->html, $prefix)) {
            $prefix = '%'.bin2hex(random_bytes(16)).':';
        }
        $blocks = [];
        $xhtml = str_contains($this->html, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML');

        try {
            // Match protected blocks and comments in document order, so text inside one
            // cannot be interpreted as markup belonging to another.
            $html = self::checked(preg_replace_callback(
                '~<!--(?<comment>.*?)-->|<(?<tag>script|style|pre|textarea)(?=[\s>])(?<attrs>(?:"[^"]*"|\'[^\']*\'|[^>"\']++)*+)>(?<body>.*?)</\k<tag>\s*>~isu',
                static function (array $match) use (&$blocks, $prefix, $xhtml): string {
                    if (! isset($match['tag'])) {
                        $comment = $match['comment'];

                        return str_starts_with($comment, '[') || str_contains($comment, '<![') || str_starts_with($comment, '#')
                            ? $match[0]
                            : '';
                    }

                    $tag = strtolower($match['tag']);
                    $content = $match[0];
                    if ($tag === 'script' || $tag === 'style') {
                        $pattern = $tag === 'script'
                            ? '/(?:^\s*<!--\s*|\s*(?:\/\/)?\s*-->\s*$)/u'
                            : '/(?:^\s*<!--|-->\s*$)/u';
                        $body = self::checked(preg_replace($pattern, '', $match['body']));
                        if (str_contains($body, '<![CDATA[')) {
                            $body = str_replace(['<![CDATA[', ']]>'], '', $body);
                        }
                        $body = trim($body);
                        if ($xhtml && (strpbrk($body, '<&') !== false || str_contains($body, '--') || str_contains($body, ']]>'))) {
                            $body = '/*<![CDATA[*/'.$body.'/*]]>*/';
                        }
                        $content = '<'.$match['tag'].$match['attrs'].'>'.$body.'</'.$match['tag'].'>';
                    }

                    $key = $prefix.count($blocks).'%';
                    $blocks[$key] = $content;

                    return $key;
                },
                $this->html,
            ));

            $html = str_replace("\r\n", "\n", trim($html));
            $html = self::checked(preg_replace('/^\s+|\s+$/mu', '', $html));
            $html = self::checked(preg_replace(
                '/\s+(<\/?(?:area|article|aside|base(?:font)?|blockquote|body'
                .'|canvas|caption|center|col(?:group)?|dd|dir|div|dl|dt|fieldset|figcaption|figure|footer|form'
                .'|frame(?:set)?|h[1-6]|head|header|hgroup|hr|html|legend|li|link|main|map|menu|meta|nav'
                .'|ol|opt(?:group|ion)|output|p|param|section|t(?:able|body|head|d|h|r|foot|itle)'
                .'|ul|video)\b[^>]*>)/iu',
                '$1',
                $html,
            ));
            $html = self::checked(preg_replace('/(<[a-z\-]+)\s+([^>]+>)/iu', "$1\n$2", $html));

            return strtr($html, $blocks);
        } catch (RuntimeException $e) {
            if (class_exists(Craft::class, false)) {
                Craft::warning('Skipping HTML minification: '.$e->getMessage(), __METHOD__);
            }

            return $this->html;
        }
    }

    private static function checked(?string $result): string
    {
        return $result ?? throw new RuntimeException(preg_last_error_msg());
    }
}
