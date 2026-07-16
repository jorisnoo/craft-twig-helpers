<?php

namespace Noo\CraftTwigHelpers\filters;

use Twig\Markup;

class TrimEmptyParagraphsFilter
{
    public function __invoke(string|Markup $html): string|Markup
    {
        $isSafe = $html instanceof Markup;

        $emptyP = '<p(?:\s[^>]*)?>(?:\s|\x{00A0}|&nbsp;|<br[^>]*\/?>)*<\/p>';

        $result = (string) $html;
        $result = preg_replace('~^(\s*'.$emptyP.'\s*)+~isu', '', $result);
        $result = preg_replace('~(\s*'.$emptyP.'\s*)+$~isu', '', $result);
        $result = trim($result);

        return $isSafe ? new Markup($result, 'UTF-8') : $result;
    }
}
