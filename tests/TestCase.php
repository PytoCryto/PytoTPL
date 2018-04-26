<?php
namespace Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PytoTPL\PytoTPL
     */
    protected $engine;

    protected function setUp()
    {
        $this->engine = new \PytoTPL\PytoTPL();

        $this->configure();
    }

    private function configure()
    {
        $this->engine->setConfig([
            'tpl_folder'      => __DIR__ . '/views/tpl/',
            'cache_folder'    => __DIR__ . '/views/cache/',
            'tpl_file_format' => '.tpl',
            'compress'        => false,
            'event_emitter'   => true,
        ]);
    }

    protected function tearDown()
    {
        $this->engine->flushCache();
    }
}
