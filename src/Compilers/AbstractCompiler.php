<?php

namespace PytoTPL\Compilers;

abstract class AbstractCompiler
{
    /**
     * The main compiler instance
     * 
     * @var \PytoTPL\Compiler
     */
    protected $compiler;

    /**
     * Set the main compiler instance
     * 
     * @param \PytoTPL\Compiler $compiler 
     * @return void
     */
    public function __construct(\PytoTPL\Compiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Returns the regex pattern for current compiler
     * 
     * @return string
     */
    public abstract function getPattern();

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public abstract function compile($matches);
}
