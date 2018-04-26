<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {-- my comment here --}
 */
class PytoTPL_COMMENT_NOPARSE extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return "/\{--(.*?)--\}/s";
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        return $this->compiler->wrapComment(str_replace(["<?php", "?>"], null, $matches[1]));
    }
}
