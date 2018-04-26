<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {section.view: section_name_here}
 */
class PytoTPL_GET_SECTION extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return '/{section\.view:(\s*)(\w+)}/';
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        return $this->compiler->wrap("echo \$__env->getSection(\"{$matches[2]}\")");
    }
}
