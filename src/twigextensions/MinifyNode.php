<?php

namespace Noo\CraftTwigHelpers\twigextensions;

use Noo\CraftTwigHelpers\MinifyHtml;
use Twig\Compiler;
use Twig\Node\Node;

class MinifyNode extends Node
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$_compiledBody = ob_get_clean();\n")
            ->write("if (!\Craft::\$app->getConfig()->getGeneral()->devMode) {\n")
            ->indent()
            ->write("\$_compiledBody = " . MinifyHtml::class . "::minify(\$_compiledBody);\n")
            ->outdent()
            ->write("}\n")
            ->write("echo \$_compiledBody;\n");
    }
}
