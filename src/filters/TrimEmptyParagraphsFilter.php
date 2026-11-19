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
        $emptyP = '<p(?:\s[^>]*+)?>(?:\s|&nbsp;|<br\b[^>]*+>)*+</p>';
        $result = preg_replace('~\A(?:\s*+'.$emptyP.')++\s*+~iu', '', (string) $html);
        // Skip entire interior runs rather than retrying the suffix from each paragraph.
        $result = $result === null ? null : preg_replace('~(?:\s*+'.$emptyP.')++\s*+(?:\z|(*SKIP)(*F))~iu', '', $result);
        if ($result === null) {
            return $html;
        }
        $result = trim($result);

        return $isSafe ? new Markup($result, 'UTF-8') : $result;
    }
}
