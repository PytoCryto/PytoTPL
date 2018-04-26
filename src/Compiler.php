<?php

namespace PytoTPL;

use PytoTPL\PytoTPL;
use PytoTPL\Exception\NotFoundException;
use PytoTPL\Exception\InvalidCompilerException;

class Compiler
{
    /**
     * A list with compilers and their order priority for proper compilation
     * 
     * @var array
     */
    private $compilers = [
        // namespace => priority
        'PytoTPL\Compilers\PytoTPL_INCR_DECR_OPERATORS' => 1,
        'PytoTPL\Compilers\PytoTPL_ECHO'                => 1,
        'PytoTPL\Compilers\PytoTPL_ECHO_NO_ESCAPE'      => 3,
        'PytoTPL\Compilers\PytoTPL_ASSIGN'              => 5,
        'PytoTPL\Compilers\PytoTPL_SHARE'               => 5,
        'PytoTPL\Compilers\PytoTPL_COMMENT_NOPARSE'     => 1,
        'PytoTPL\Compilers\PytoTPL_SECTION'             => 5,
        'PytoTPL\Compilers\PytoTPL_GET_SECTION'         => 5,
        'PytoTPL\Compilers\PytoTPL_STATEMENTS'          => 6,
        'PytoTPL\Compilers\PytoTPL_EXTENDS_INCLUDE'     => 6,
        'PytoTPL\Compilers\PytoTPL_FUNCTION'            => 1,
        'PytoTPL\Compilers\PytoTPL_END_STATEMENTS'      => 6,
        'PytoTPL\Compilers\PytoTPL_ELSE_BREAK_CONTINUE' => 6,
        'PytoTPL\Compilers\PytoTPL_CUSTOM_DIRECTIVES'   => 1,
    ];

    /**
     * The engine instance
     * 
     * @var \PytoTPL\PytoTPL
     */
    protected $engine;

    /**
     * The template configuration
     * 
     * @var array
     */
    protected $config;

    /**
     * Create a new compiler instance
     * 
     * @param  \PytoTPL\PytoTPL $engine 
     * @param  array        $config 
     * @return void
     */
    public function __construct(PytoTPL $engine, array $config)
    {
        $this->config = $config;

        arsort($this->compilers);

        $this->engine = $engine;
    }

    /**
     * Get the engine instance
     * 
     * @return \PytoTPL\PytoTPL
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Get the list of compilers
     * 
     * @return array
     */
    public function getCompilers()
    {
        return $this->compilers;
    }

    /**
     * Compile the template contents
     * 
     * @param  string $content 
     * @return mixed
     */
    public function compile($content)
    {
        return $this->tryCompile($content, null, null);
    }

    /**
     * Load all the available compilers and run each one
     * 
     * @param  string        $string 
     * @param  \Closure|null $afterEvents 
     * @param  mixed         $flags 
     * @return type
     */
    private function tryCompile($string, \Closure $afterEvents = null, $flags = null) // todo: after events + flags (?)
    {
        foreach ($this->compilers as $compiler => $priority) {
            if (! class_exists($compiler)) {
                throw new NotFoundException("PytoTPL compiler ({$compiler}) couldn't be found!");
            } elseif (! is_subclass_of($compiler, "PytoTPL\Compilers\AbstractCompiler")) {
                throw new InvalidCompilerException("Compiler ({$compiler}) must extend (PytoTPL\Compilers\AbstractCompiler)!");
            }

            $string = $this->run($compiler, $string);
        }

        return $string;
    }

    /**
     * Run the compiler
     * 
     * @param  string $compiler 
     * @param  string $string 
     * @return mixed
     */
    private function run($compiler, $string)
    {
        $compiler = $this->getCompilerInstance($compiler);

        return preg_replace_callback($compiler->getPattern(), function ($matches) use ($compiler) {
            return $compiler->compile($matches);
        }, $string);
    }

    /**
     * Create and return a new instance of the compiler
     * 
     * @param  string $compiler 
     * @return obj
     */
    private function getCompilerInstance($compiler)
    {
        return new $compiler($this);
    }

    /**
     * Wrap the given value in PHP tags
     * 
     * @param  string      $content 
     * @param  null|string $end 
     * @param  bool        $newLine 
     * @return string
     */
    public function wrap($content, $end = ';', $newLine = false)
    {
        return  '<?php ' . $content . $end . ' ?>' . ($newLine === true ? PHP_EOL : '');
    }

    /**
     * Wrap the given value into a valid PHP comment
     * 
     * @param  string $content 
     * @return string
     */
    public function wrapComment($content)
    {
        return $this->wrap("/* {$content} */", null, true);
    }

    /**
     * Description
     * 
     * @param  string $variable 
     * @param  int    $depth 
     * @return mixed
     */
    public function stringDotsToArrayKeys($variable, $depth = 7)
    {
        /**
         *
         * -- Todo: Rewrite this regex --
         *  
        */
        return preg_replace_callback('/\$(\w+)\.(\w+)(\.(\w+)(\.(\w+)(\.(\w+)(\.(\w+)(\.(\w+)?)?)?)?)?)?/s', function ($matches) {
            switch (count($matches)) {
                case 3:
                    $var = $this->calculateSubKeys($matches, 1);
                break;

                case 5:
                    $var = $this->calculateSubKeys($matches, 2);
                break;

                case 7:
                    $var = $this->calculateSubKeys($matches, 3);
                break;

                case 9:
                    $var = $this->calculateSubKeys($matches, 4);
                break;

                case 11:
                    $var = $this->calculateSubKeys($matches, 5);
                break;

                case 13:
                    $var = $this->calculateSubKeys($matches, 6);
                break;

                case 14:
                    $var = $this->calculateSubKeys($matches, 7);
                break;

                default:
                    $var = $variable;
            }

            return $var;
        },
        $variable);
    }

    /**
     * Description
     * 
     * @param  array $matches 
     * @param  int   $amount 
     * @param  int   $times 
     * @return string
     */
    private function calculateSubKeys(array $matches = [], $amount, $times = 2)
    {
        if (empty($matches)) {
            return;
        }

        $j = $times;

        for ($i = 0; $i < $amount; $i++) {
            $var[] = "[\"{$matches[$j]}\"]";

            $j += $times;
        }

        return "\${$matches[1]}" . join(null, $var);
    }
}
