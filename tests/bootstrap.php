<?php

require __DIR__ . '/../vendor/autoload.php';

$tpl = new PytoTPL\PytoTPL(); // create the instance

$tpl->setConfig([
    'tpl_folder'      => __DIR__ . '/views/tpl/',
    'cache_folder'    => __DIR__ . '/views/cache/',
    'tpl_file_format' => '.tpl',
    'compress'        => false, // compression only available in a valid <HTML> document
    'event_emitter'   => true, // set to false for better performance
]);

/*
Available events:

exception   = This event gets dispatched whenever an exception occur during the rendering| Args: $exception (The exception object)
fatal.error = This event gets dispatched whenever a fatal error during the rendering/compilation. Args: $exception (The exception object)

before.compile = This event gets dispatched before the template gets compiled. Args: $file (The tpl filename)
after.compile  = This event gets dispatched after the template gets compiled. Args: $file (The tpl filename)

rendering.incremented = This event gets dispatched when the rendering level gets incremented. Args: $count (The rendering level)
rendering.decremented = This event gets dispatched when the rendering level gets decremented. Args: $count (The rendering level)
rendering.completed   = This event gets dispatched when the rendering is completed. Args: $count (The rendering level)

-- How to listen to an event, make sure 'event_emitter' is set to true in your configuration! --

$tpl->on('exception', function($exception) {
    // do something with $exception 
});
*/
