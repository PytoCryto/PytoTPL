<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {var $anything = 'valuue'}, {assign $a = 'abc'}
 */
class PytoTPL_ASSIGN extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return "/{(assign|var)\s+(.+?)}/s";
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        $variable = rtrim($matches[2], ';');
        
        return $this->compiler->wrap(
            $this->compiler->stringDotsToArrayKeys($variable)
        );
    }
}
