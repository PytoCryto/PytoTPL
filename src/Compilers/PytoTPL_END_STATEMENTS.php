<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {/if}, {/foreach}, {/for}, {/while}, {/isset}, {/empty}
 */
class PytoTPL_END_STATEMENTS extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return "/{\/(if|foreach|for|while|isset|empty)}/";
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        if (in_array($statement = $matches[1], ['isset', 'empty'])) { // {isset: $var} shortcuts
            $statement = "if";
        }

        return $this->compiler->wrap("end{$statement}");
    }
}
