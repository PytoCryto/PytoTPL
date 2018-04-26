<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {share $var = 'value'}
 */
class PytoTPL_SHARE extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return '/\{share\s+(?:\$)(\w+)\s*=\s*(.+?)\}/s';
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        $variable = $matches[1];
        $value = rtrim($matches[2], ';');
        
        return $this->compiler->wrap("\$__env->setShared(\"{$variable}\", {$value})");
    }
}
