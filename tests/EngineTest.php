<?php

namespace Tests;

use Exception;
use PytoTPL\PytoTPL;
use PytoTPL\Exception\NotFoundException;
use PytoTPL\Exception\InvalidConfigException;

class EngineTest extends TestCase
{
    public function testSetConfig()
    {
        $this->expectException(InvalidConfigException::class);

        (new PytoTPL())->setConfig([]);
    }

    public function testGetConfig()
    {
        $this->assertEquals('.tpl', $this->engine->getConfig('tpl_file_format'));

        $this->assertEquals(123, $this->engine->getConfig('age', 123));

        $this->assertNull($this->engine->getConfig('i_dont_exist'));
    }

    public function testRenderNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $this->engine->render('header');
    }

    public function testRenderString()
    {
        $string = $this->engine->renderString('Hi {$name}', ['name' => 'John Doe']);

        $this->assertEquals('Hi John Doe', $string);
    }

    public function testRenderTemplate()
    {
        $template = $this->engine->render('home', $data = ['name' => 'John Doe']);

        $this->assertEquals('Hello, my name is John Doe', $template);
    }

    public function testAssignData()
    {
        $data = [
            'username' => 'John Doe',
            'email'    => 'test@example.com',
        ];

        $this->engine->assign($data)
                        ->assign('love', true)
                        ->assign('hate', false);

        $this->assertEquals('John Doe', $this->engine->get('username'));
        $this->assertEquals('test@example.com', $this->engine->get('email'));

        $this->assertTrue($this->engine->get('love'));
        $this->assertFalse($this->engine->get('hate'));

        $this->assertNull($this->engine->get('i_dont_exist'));
    }

    public function testSharedData()
    {
        $data = ['age' => rand(20, 200)];

        $this->engine->share($data);

        $this->assertTrue($this->engine->hasShared('age'));
        $this->assertEquals($data['age'], $this->engine->getShared()['age']);
        $this->assertCount(1, $this->engine->getShared());
        
        $this->engine->flushSharedData();

        $this->assertEmpty($this->engine->getShared());
    }

    public function testHasData()
    {
        $this->assertFalse($this->engine->has('i_dont_exist'));

        $this->engine->assign('i_dont_exist', 'i do exist');

        $this->assertTrue($this->engine->has('i_dont_exist'));
    }

    public function testCustomDirective()
    {
        $this->engine->directive('test_directive', function ($expression) {
            return '<?php echo "You like ' . $expression . '!"; ?>';
        });

        $directive = $this->engine->getCustomDirective('test_directive');

        $this->assertEquals('<?php echo "You like pizza!"; ?>', $directive('pizza'));

        $this->assertCount(1, $this->engine->getCustomDirectives());
    }
}
