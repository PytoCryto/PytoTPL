<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {function: $obj->test('hi!')}
 */
class PytoTPL_FUNCTION extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return '/{function:(\s*)(.*?)}/';
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        return $this->compiler->wrap(
            $this->compiler->stringDotsToArrayKeys($matches[2])
        );
    }
}
