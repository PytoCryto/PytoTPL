<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {else}, {continue: condition here?}, {break: condition here?}
 */
class PytoTPL_ELSE_BREAK_CONTINUE extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return '/{(else|continue|break)(:(\s*)(.*))?}/';
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

        if ($variable != "else") {
            if (isset($matches[4]) && !empty($matches[4])) {
                $expression = $this->compiler->stringDotsToArrayKeys($matches[4]);

                $variable = "if({$expression}) { {$variable}; }";
            } else {
                $variable .= ";";
            }
        } else {
            $variable .= ":";
        }

        return $this->compiler->wrap($variable, null);
    }
}
