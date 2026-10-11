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

        // Twig 3.9+ compiles templates to generators (nodes must yield, not echo),
        // so the body is captured the same way Twig's own CaptureNode does it.
        // Older Twig versions (Craft 4.0 ships ~3.3) only support echo/ob capture.
        $env = $compiler->getEnvironment();
        $supportsYield = method_exists($env, 'useYield');
        $useYield = $supportsYield && $env->useYield();

        if ($supportsYield) {
            $compiler
                ->write('$_compiledBody = ')
                ->raw($useYield ? "implode('', iterator_to_array(" : '\\Twig\\Extension\\CoreExtension::captureOutput(')
                ->raw("(function () use (&\$context, \$macros, \$blocks) {\n")
                ->indent()
                ->subcompile($this->getNode('body'))
                ->write("yield from [];\n")
                ->outdent()
                ->write('})()')
                ->raw($useYield ? ", false));\n" : ");\n");
        } else {
            $compiler
                ->write("ob_start();\n")
                ->write("try {\n")
                ->indent()
                ->subcompile($this->getNode('body'))
                ->write("\$_compiledBody = ob_get_clean();\n")
                ->outdent()
                ->write("} catch (\\Throwable \$e) {\n")
                ->indent()
                ->write("ob_end_clean();\n")
                ->write("throw \$e;\n")
                ->outdent()
                ->write("}\n");
        }

        $compiler
            ->write("if (!\\Craft::\$app->getConfig()->getGeneral()->devMode) {\n")
            ->indent()
            ->write('$_compiledBody = \\'.MinifyHtml::class."::minify(\$_compiledBody);\n")
            ->outdent()
            ->write("}\n")
            ->write($supportsYield ? "yield \$_compiledBody;\n" : "echo \$_compiledBody;\n");
    }
}
