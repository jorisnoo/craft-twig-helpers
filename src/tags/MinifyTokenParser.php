<?php

namespace Noo\CraftTwigHelpers\tags;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class MinifyTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): MinifyNode
    {
        $lineNo = $token->getLine();
        $stream = $this->parser->getStream();

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse($this->decideMinifyEnd(...), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new MinifyNode(['body' => $body], [], $lineNo, $this->getTag());
    }

    public function getTag(): string
    {
        return 'minify';
    }

    public function decideMinifyEnd(Token $token): bool
    {
        return $token->test('endminify');
    }
}
