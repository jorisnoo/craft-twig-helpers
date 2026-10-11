<?php

namespace Noo\CraftTwigHelpers\filters;

use Twig\Markup;

class TrimEmptyParagraphsFilter
{
    public function __invoke(string|Markup|null $html): string|Markup
    {
        if ($html === null) {
            return '';
        }

        $isSafe = $html instanceof Markup;

        $emptyP = '<p(?:\s[^>]*)?>(?:\s|\x{00A0}|&nbsp;|<br[^>]*\/?>)*<\/p>';

        $input = (string) $html;
        $result = preg_replace('~^(\s*'.$emptyP.'\s*)+~isu', '', $input);
        $result = $result === null ? null : preg_replace('~(\s*'.$emptyP.'\s*)+$~isu', '', $result);

        // On PCRE failure (e.g. invalid UTF-8, backtrack limit) keep the original content.
        $result = $result === null ? $input : trim($result);

        return $isSafe ? new Markup($result, 'UTF-8') : $result;
    }
}
