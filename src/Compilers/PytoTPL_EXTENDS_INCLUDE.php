<?php

namespace PytoTPL\Compilers;

use PytoTPL\Compilers\AbstractCompiler;
/**
 * Usage: {extend "header"}, {extends "header"}
 */
class PytoTPL_EXTENDS_INCLUDE extends AbstractCompiler
{
    /**
     * Get the regex pattern for current compiler
     * 
     * @return string
     */
    public function getPattern()
    {
        return "/{(extends|extend|include|includes)(\s*)\"(\w.*?)\"(?:,(\s*)(.*?))?}/is";
    }

    /**
     * Pass all the regex matches to current compiler
     * 
     * @param  array $matches 
     * @return string
     */
    public function compile($matches)
    {
        if (in_array($matches[1], ["include", "includes"])) {
            // todo: Fix dis shet :c
        } else {
            if (isset($matches[5])) {
                $params    = $matches[5];
                $newParams = [];

                foreach (explode(",", $params) as $param) {
                    $newParams[] = preg_replace_callback("/(\w+)=(.*)/is", function ($m) {
                        if (! preg_match("/^(?:\"|')/is", $m[2])) {
                            $m[2] = "\"$m[2]\"";
                        }

                        return "\"{$m[1]}\" => {$m[2]}";
                    }, $param);
                }

                return $this->compiler->wrap("echo \$__env->render(\"{$matches[3]}\", [" . join(",", $newParams) . "])");
            } else {
                return $this->compiler->wrap("echo \$__env->render(\"{$matches[3]}\")");
            }
        }
    }
}
