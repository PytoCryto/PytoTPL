<?php

namespace Tests;

use PytoTPL\PytoTPL;
use PytoTPL\Compiler;
use PytoTPL\Exception\NotFoundException;
use PytoTPL\Exception\InvalidCompilerException;

class CompilerTest extends TestCase
{
    /**
     * @var \PytoTPL\Compiler
     */
    protected $compiler;

    protected function setUp()
    {
        parent::setUp();

        $this->compiler = $this->engine->getCompiler();
    }

    public function testEngineInstance()
    {
        $this->assertInstanceOf(PytoTPL::class, $this->compiler->getEngine());
    }

    public function testCompilerInstance()
    {
        $this->assertInstanceOf(Compiler::class, $this->compiler);
    }

    public function testWrapContent()
    {
        $this->assertEquals('<?php echo 123; ?>', $this->compiler->wrap('echo 123'));

        $this->assertEquals('<?php echo 123 ?>', $this->compiler->wrap('echo 123', null));

        $this->assertEquals('<?php echo 123 ?>' . PHP_EOL, $this->compiler->wrap('echo 123', null, true));
    }

    public function testWrapComment()
    {
        $this->assertEquals('<?php /* I love pizza! */ ?>' . PHP_EOL, $this->compiler->wrapComment('I love pizza!'));
    }

    public function testStringDotsToArrayKeys()
    {
        $this->assertEquals('$user["data"]["age"]', $this->compiler->stringDotsToArrayKeys('$user.data.age'));
    }
}
