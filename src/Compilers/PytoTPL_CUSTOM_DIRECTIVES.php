<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {directive_name: $args}
 */
class PytoTPL_CUSTOM_DIRECTIVES extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return sprintf("/\{(%s)(?::\s*(.*?))\s*\}/s", $this->makeDynamicPattern());
    }

    private function makeDynamicPattern()
    {
        return join('|', array_keys($this->compiler->getEngine()->getCustomDirectives()));
    }

    private function callDirective($name, $value)
    {
        return call_user_func(
            $this->compiler->getEngine()->getCustomDirective($name),
            trim($value),
            $this->compiler
        );
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        list($_, $directive, $expression) = $matches;

        return $this->callDirective($directive, $expression);
    }
}
