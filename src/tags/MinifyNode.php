<?php

namespace Noo\CraftTwigHelpers\tags;

use Noo\CraftTwigHelpers\MinifyHtml;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Node;

#[YieldReady]
class MinifyNode extends Node
{
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        if (method_exists($compiler->getEnvironment(), 'useYield')) {
            $useYield = $compiler->getEnvironment()->useYield();
            $compiler
                ->write('$_minifyBody = ')
                ->raw($useYield ? "implode('', iterator_to_array(" : '\\Twig\\Extension\\CoreExtension::captureOutput(')
                ->raw("(function () use (&\$context, \$macros, \$blocks) {\n")
                ->indent()
                ->subcompile($this->getNode('body'))
                ->write("return; yield;\n")
                ->outdent()
                ->write('})()')
                ->raw($useYield ? ", false));\n" : ");\n");
            $output = 'yield';
        } else {
            // Older Twig releases bundled with Craft 4 render their nodes with echo.
            $compiler
                ->write("ob_start();\n")
                ->write("try {\n")
                ->indent()
                ->subcompile($this->getNode('body'))
                ->write("\$_minifyBody = ob_get_contents();\n")
                ->outdent()
                ->write("} finally {\n")
                ->indent()
                ->write("ob_end_clean();\n")
                ->outdent()
                ->write("}\n");
            $output = 'echo';
        }

        $compiler->write($output.' \Craft::$app->getConfig()->getGeneral()->devMode ? $_minifyBody : \\'.MinifyHtml::class."::minify(\$_minifyBody);\n");
    }
}
