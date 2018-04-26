<?php
/**
 * PytoTPL global helpers
 */
if (! function_exists('pp')) {
    function pp($s, $die = false)
    {
        echo '<pre>';
        print_r($s);
        echo '</pre><hr>';

        if ($die)
            die;
    }
}

if (! function_exists('tplEscape')) {
    function tplEscape($str, $encoding = 'utf-8')
    {
        return htmlentities($str, ENT_QUOTES, $encoding);
    }
}

if (! function_exists('tplUnescape')) {
    function tplUnescape($str, $encoding = 'utf-8')
    {
        return $str; //html_entity_decode($str, ENT_QUOTES, $encoding);
    }
}

if (! function_exists('isJson')) {
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (! function_exists('benchmark')) {
    function benchmark($from, $params = [])
    {
        $total = microtime(true) - $from;

        if ($total < 0.3) {
            $i = 'green';
        } elseif ($total >= 0.3 || $total < 0.55) {
            $i = 'orange';
        } elseif ($total >= 0.55) {
            $i = 'red'; 
        }

        printf("<span style='opacity:.85;z-index:9999;font-family:monospace;text-shadow:#000 0 0 3px;padding:5px;color:#fff;font-weight:bold;background:%s;position:fixed;right:0;bottom:0;'>Executed in %s seconds %s</span>", $i, $total, implode(' | ', $params));
    }
}
