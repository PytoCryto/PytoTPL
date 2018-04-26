<?php

namespace Tests;

use Exception;

class EventTest extends TestCase
{
    public function testEvents()
    {
        $this->engine->on('rendering.incremented', function ($renderCounter) {
            $this->assertEquals(1, $renderCounter);
        });

        $this->engine->on('rendering.decremented', function ($renderCounter) {
            $this->assertEquals(0, $renderCounter);
        });

        $this->engine->on('rendering.completed', function ($renderCounter) {
            $this->assertEquals(0, $renderCounter);
        });

        $this->engine->on('before.compile', function ($file) {
            $this->assertEquals($file, $this->engine->getFile());

            $this->assertStringEndsWith('home.tpl', $file);
        });

        $this->engine->on('after.compile', function ($file) {
            $this->assertEquals($file, $this->engine->getFile());

            $this->assertStringEndsWith('home.tpl', $file);
        });

        $this->engine->render('home', ['name' => 'John Doe']);
    }

    public function testExceptionEvent()
    {
        $this->expectException(Exception::class);

        $this->engine->on('exception', function (Exception $e) {
            $this->assertEquals('Hello', $e->getMessage());
        });

        $this->engine->render('throw_exception');
    }
}
