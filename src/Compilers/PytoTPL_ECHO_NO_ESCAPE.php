<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
use PytoTPL\Traits\TernaryAwareTrait;
/**
 * Usage: {!!$var!!}
 */
class PytoTPL_ECHO_NO_ESCAPE extends AbstractCompiler
{
	use TernaryAwareTrait;

    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return '/\{!!\s*(.+?)\s*!!\s*\}(\r?\n)?/s';
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        $variable = $this->compileTernaryOperators($this->compiler->stringDotsToArrayKeys($matches[1]));

        $whitespace = empty($matches[2]) ? '' : $matches[2] . $matches[2];

        return $this->compiler->wrap("echo {$variable}") . $whitespace;
    }
}
