<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {$i++}, {$i--}
 */
class PytoTPL_INCR_DECR_OPERATORS extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return "/{(.+?)(\++|\-+)}/x";
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        $variable = $matches[1] . $matches[2];
        
        return $this->compiler->wrap(
            $variable
        );
    }
}
