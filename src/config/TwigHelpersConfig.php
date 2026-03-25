<?php

namespace Noo\CraftTwigHelpers\config;

use craft\config\BaseConfig;

class TwigHelpersConfig extends BaseConfig
{
    public array $filters = [];

    public array $functions = [];

    public array $globals = [];

    public array $tests = [];

    public function filters(array $filters = []): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function functions(array $functions = []): self
    {
        $this->functions = $functions;

        return $this;
    }

    public function globals(array $globals = []): self
    {
        $this->globals = $globals;

        return $this;
    }

    public function tests(array $tests = []): self
    {
        $this->tests = $tests;

        return $this;
    }
}
