<?php

namespace Noo\CraftTwigHelpers\filters;

class TrimEmptyParagraphsFilter
{
    public function __invoke(string $html): string
    {
        $emptyP = '<p(?:\s[^>]*)?>(?:\s|\x{00A0}|&nbsp;|<br[^>]*\/?>)*<\/p>';

        $html = preg_replace('~^(\s*'.$emptyP.'\s*)+~isu', '', $html);
        $html = preg_replace('~(\s*'.$emptyP.'\s*)+$~isu', '', $html);

        return trim($html);
    }
}
