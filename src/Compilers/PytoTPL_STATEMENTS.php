<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {if: condition}, {foreach: condition}, {for: condition}, {while: condition}, {isset: condition}, {empty: condition}
 */
class PytoTPL_STATEMENTS extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return "/{(if|foreach|elseif|for|while|isset|empty):\s+(.+?)}/x";
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        $expression = $this->compiler->stringDotsToArrayKeys($matches[2]);

        if (in_array($statement = $matches[1], ['isset', 'empty'])) { // {isset: $var} shortcuts
            $variable = "if({$statement}({$expression})):";
        } else {
            $variable = "{$statement}({$expression}):";
        }

        if ($statement == "foreach") {
            
        }

        return $this->compiler->wrap($variable, null);
    }
}
