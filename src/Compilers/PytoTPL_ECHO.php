<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
use PytoTPL\Traits\TernaryAwareTrait;
/**
 * Usage: {$var}, {echo $var}, {print $var}
 */
class PytoTPL_ECHO extends AbstractCompiler
{
    use TernaryAwareTrait;

    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return '/\{(echo|print|\$)\s*(.+?)(?:\|(.*?))?\s*\}(\r?\n)?/s';
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        if ($matches[1] == '$') {
            $matches[2] = '$' . $matches[2];
        }

        $variable = $this->compileTernaryOperators($this->compiler->stringDotsToArrayKeys($matches[2]));

        $whitespace = empty($matches[4]) ? '' : $matches[4] . $matches[4];

        if (! empty($matches[3])) {
            $function = $matches[3];

            if (strpos($function, ":") !== false) {
                list($function, $args) = explode(":", $function, 2);

                $function .= "({$variable},{$args}";
            } else {
                $function .= "({$variable}";
            }

            $function .= ")";

            $variable = $function;
        }

        return $this->compiler->wrap("echo tplEscape({$variable})") . $whitespace;
    }
}
