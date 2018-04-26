<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {section: my_section} ... {/section}, {section.overwrite: my_section} ... {/section},
 */
class PytoTPL_SECTION extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return '/{(section\.overwrite|section):(\s*)(\w+)(,(\s*)(.*?))?}(.*?){\/section}/s';
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        $overwrite = ($matches[1] == "section.overwrite") ? "true" : "false";

        if (isset($matches[6]) && !empty($matches[6])) {
            /*
            $params = $this->compiler->splitParams($matches[6], "default", function($values) {
                return $values;
            });
            
            $input = null;

            foreach($params as $key => $value)
                $input .= PHP_EOL . "if(!isset(\$$key))" . PHP_EOL . "{" . PHP_EOL . "\$$key = <<<HTML" . PHP_EOL . "$value" . PHP_EOL . "HTML;" . PHP_EOL . "}";

            $input = $this->compiler->wrap($input, "");

            return
                $this->compiler->wrap("ob_start()")
                . $input
                . $matches[7]
                . $this->compiler->wrap("\$__env->addSection(\"{$matches[3]}\", ob_get_contents(), $overwrite); ob_end_clean()");
            */
        }

        return
            $this->compiler->wrap("ob_start()")
            . $matches[7]
            . $this->compiler->wrap("\$__env->addSection(\"{$matches[3]}\", ob_get_contents(), $overwrite); ob_end_clean()");
    }
}
