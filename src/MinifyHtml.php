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

class MinifyHtml
{
    protected string $html;

    protected ?bool $isXhtml = null;

    protected string $replacementHash;

    /** @var array<string, string> */
    protected array $placeholders = [];

    public static function minify(string $html): string
    {
        $min = new self($html);

        return $min->process();
    }

    public function __construct(string $html)
    {
        $this->html = str_replace("\r\n", "\n", trim($html));
    }

    public function process(): string
    {
        if ($this->isXhtml === null) {
            $this->isXhtml = (strpos($this->html, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML') !== false);
        }

        $this->replacementHash = 'MINIFYHTML'.md5((string) ($_SERVER['REQUEST_TIME'] ?? ''));
        $this->placeholders = [];

        // Replace <script> blocks with placeholders
        $this->html = preg_replace_callback(
            '/(\s*)<script(\b[^>]*?>)([\s\S]*?)<\/script>(\s*)/iu',
            $this->removeScriptCB(...),
            $this->html,
        );

        // Replace <style> blocks with placeholders
        $this->html = preg_replace_callback(
            '/\s*<style(\b[^>]*>)([\s\S]*?)<\/style>\s*/iu',
            $this->removeStyleCB(...),
            $this->html,
        );

        // Remove HTML comments (preserve IE conditional comments)
        $this->html = preg_replace_callback(
            '/<!--([\s\S]*?)-->/u',
            $this->commentCB(...),
            $this->html,
        );

        // Replace <pre> blocks with placeholders
        $this->html = preg_replace_callback(
            '/\s*<pre(\b[^>]*?>[\s\S]*?<\/pre>)\s*/iu',
            $this->removePreCB(...),
            $this->html,
        );

        // Replace <textarea> blocks with placeholders
        $this->html = preg_replace_callback(
            '/\s*<textarea(\b[^>]*?>[\s\S]*?<\/textarea>)\s*/iu',
            $this->removeTextareaCB(...),
            $this->html,
        );

        // Trim each line
        $this->html = preg_replace('/^\s+|\s+$/mu', '', $this->html);

        // Remove whitespace around block-level elements
        $this->html = preg_replace(
            '/\s+(<\/?(?:area|article|aside|base(?:font)?|blockquote|body'
            .'|canvas|caption|center|col(?:group)?|dd|dir|div|dl|dt|fieldset|figcaption|figure|footer|form'
            .'|frame(?:set)?|h[1-6]|head|header|hgroup|hr|html|legend|li|link|main|map|menu|meta|nav'
            .'|ol|opt(?:group|ion)|output|p|param|section|t(?:able|body|head|d|h||r|foot|itle)'
            .'|ul|video)\b[^>]*>)/iu',
            '$1',
            $this->html,
        );

        // Collapse whitespace outside of tags
        $this->html = preg_replace(
            '/>(\s(?:\s*))?([^<]+)(\s(?:\s*))?</u',
            '>$1$2$3<',
            $this->html,
        );

        // Use newlines before first attribute in open tags (to limit line lengths)
        $this->html = preg_replace('/(<[a-z\-]+)\s+([^>]+>)/iu', "$1\n$2", $this->html);

        // Fill placeholders (two passes for nested cases)
        $this->html = str_replace(
            array_keys($this->placeholders),
            array_values($this->placeholders),
            $this->html,
        );
        $this->html = str_replace(
            array_keys($this->placeholders),
            array_values($this->placeholders),
            $this->html,
        );

        return $this->html;
    }

    /**
     * @param  array<int, string>  $m
     */
    protected function commentCB(array $m): string
    {
        return (str_starts_with($m[1], '[') || str_contains($m[1], '<![') || str_starts_with($m[1], '#'))
            ? $m[0]
            : '';
    }

    protected function reservePlace(string $content): string
    {
        $placeholder = '%'.$this->replacementHash.count($this->placeholders).'%';
        $this->placeholders[$placeholder] = $content;

        return $placeholder;
    }

    /**
     * @param  array<int, string>  $m
     */
    protected function removePreCB(array $m): string
    {
        return $this->reservePlace("<pre{$m[1]}");
    }

    /**
     * @param  array<int, string>  $m
     */
    protected function removeTextareaCB(array $m): string
    {
        return $this->reservePlace("<textarea{$m[1]}");
    }

    /**
     * @param  array<int, string>  $m
     */
    protected function removeStyleCB(array $m): string
    {
        $openStyle = "<style{$m[1]}";
        $css = $m[2];

        // Remove HTML comments and CDATA markers
        $css = preg_replace('/(?:^\s*<!--|-->\s*$)/u', '', $css);
        $css = $this->removeCdata($css);
        $css = trim($css);

        return $this->reservePlace(
            $this->needsCdata($css)
                ? "{$openStyle}/*<![CDATA[*/{$css}/*]]>*/</style>"
                : "{$openStyle}{$css}</style>",
        );
    }

    /**
     * @param  array<int, string>  $m
     */
    protected function removeScriptCB(array $m): string
    {
        $openScript = "<script{$m[2]}";
        $js = $m[3];

        $ws1 = ($m[1] === '') ? '' : ' ';
        $ws2 = ($m[4] === '') ? '' : ' ';

        // Remove HTML comments and CDATA markers
        $js = preg_replace('/(?:^\s*<!--\s*|\s*(?:\/\/)?\s*-->\s*$)/u', '', $js);
        $js = $this->removeCdata($js);
        $js = trim($js);

        return $this->reservePlace(
            $this->needsCdata($js)
                ? "{$ws1}{$openScript}/*<![CDATA[*/{$js}/*]]>*/</script>{$ws2}"
                : "{$ws1}{$openScript}{$js}</script>{$ws2}",
        );
    }

    protected function removeCdata(string $str): string
    {
        return str_contains($str, '<![CDATA[')
            ? str_replace(['<![CDATA[', ']]>'], '', $str)
            : $str;
    }

    protected function needsCdata(string $str): bool
    {
        return $this->isXhtml && (bool) preg_match('/(?:[<&]|\-\-|\]\]>)/u', $str);
    }
}
