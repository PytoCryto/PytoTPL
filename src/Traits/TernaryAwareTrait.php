<?php

namespace PytoTPL\Traits;

trait TernaryAwareTrait
{
    public function compileTernaryOperators($expression)
    {
        // regex pattern from Laravel Blade templating engine
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $expression);
    }
}
