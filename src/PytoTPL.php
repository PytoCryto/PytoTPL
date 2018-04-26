<?php

namespace PytoTPL;

use Closure;
use Throwable;
use Exception;
use BadMethodCallException;
use PytoTPL\Traits\SectionsAwareTrait;
use PytoTPL\Traits\SharedDataAwareTrait;
use PytoTPL\Exception\PytoTPL_Exception;
use PytoTPL\Exception\NotFoundException;
use PytoTPL\Exception\InvalidConfigException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class PytoTPL
{
    use SharedDataAwareTrait,
        SectionsAwareTrait;

    const blockDirectAccessMSG = "if(!class_exists('%s')) { die('Direct access not allowed!'); }";
    const cacheFilePrefix      = "PytoTPL_CACHE";

    /**
     * The instance of the compiler
     * 
     * @var \PytoTPL\Compiler
     */
    protected $compiler;

    /**
     * The instance of the Event emitter
     * 
     * @var \Evenement\EventEmitter
     */
    protected $eventEmitter;

    /**
     * Indicates if the Event emitter is enabled
     * 
     * @var bool
     */
    protected $isEmitterEnabled;

    /**
     * The array of view data
     * 
     * @var array
     */
    protected $vars = [];

    /**
     * The array of configurable paths and cache
     * 
     * @var array
     */
    protected $config = [];

    /**
     * The name of the view
     * 
     * @var string
     */
    protected $file = null;

    /**
     * The array of custom directives
     * 
     * @var array
     */
    protected $customDirectives = [];

    /**
     * The current amount of views rendered
     * 
     * @var int
     */
    protected $renderCounter = 0;
    
    protected static $debug  = true; // Not in use yet.. ¯\_(ツ)_/¯

    /**
     * Create a new PytoTPL instance
     * 
     * @param  array $config 
     * @return void
     */
    public function __construct(array $config = [])
    {
        if (! empty($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * Create and return an instance of the compiler
     * 
     * @return \PytoTPL\Compiler
     */
    public function getCompiler()
    {
        if ($this->compiler == null) {
            $this->compiler = new \PytoTPL\Compiler($this, $this->config);
        }

        return $this->compiler;
    }

    /*
     * -- EVENT STUFF --
     * @TODO: Relocate methods below
    */

    /**
     * Return an instance of the Event emitter
     * 
     * @return \Evenement\EventEmitter
     */
    public function getEventEmitter()
    {
        if ($this->eventEmitter == null) {
            $this->eventEmitter = new \Evenement\EventEmitter();
        }

        return $this->eventEmitter;
    }

    /**
     * Indicate if the Event emitter should be enabled
     * 
     * @return bool
     */
    private function isEventEmitterEnabled()
    {
        if (! isset($this->isEmitterEnabled)) {
            $this->isEmitterEnabled = $this->getConfig('event_emitter') != false;
        }

        return $this->isEmitterEnabled;
    }

    /**
     * Dispatch/Emit an event if the Emitter is enabled
     * 
     * @param  string     $event
     * @param  mixed|null $args 
     * @return mixed
     */
    private function dispatchEvent($event, $args = null)
    {
        return $this->onEventEmitterEnabled(function ($emitter) use($event, $args) {
            return $emitter->emit($event, ! is_array($args) ? [$args] : $args);
        });
    }

    /**
     * Execute the given callback if the Emitter is enabled
     * 
     * @param  \Closure $callback 
     * @return mixed
     */
    private function onEventEmitterEnabled(Closure $callback)
    {
        if ($this->isEventEmitterEnabled()) {
            return $callback($this->getEventEmitter());
        }
    }

    /**
     * Check if the required configuration keys are provided
     * 
     * @return void
     */
    protected function validateConfig()
    {
        $defaults = [
            'tpl_folder',
            'cache_folder',
            'tpl_file_format',
            //'compress',
            //'event_emitter',
        ];

        foreach ($defaults as $key) {
            if (! isset($this->config[$key])) {
                throw new InvalidConfigException(sprintf("Missing a configuration key (%s)", $key));
            }
        }
    }

    /**
     * Set the configuration
     * 
     * @param  array     $config 
     * @param  null|bool $merge 
     * @return $this
     */
    public function setConfig(array $config, $merge = false)
    {
        $this->config = ($merge)
            ? array_merge($this->config, $config)
            : $config;

        $this->validateConfig();

        return $this;
    }

    /**
     * Get the specified configuration value
     * 
     * @param  string     $key 
     * @param  mixed|null $default 
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return isset($this->config[$key])
            ? $this->config[$key]
            : $default;
    }

    /**
     * Share data across all views
     * 
     * @param  string|array $key 
     * @param  mixed|null   $value 
     * @return $this
     */
    public function share($key, $value = null)
    {
        $this->setShared($key, $value);

        return $this;
    }

    /**
     * Assign a value to a key
     * 
     * @param  string|array $key 
     * @param  mixed|null   $value 
     * @return $this
     */
    public function assign($key, $value = null)
    {
        if (! is_array($key) || $value instanceof Closure) {
            $this->vars[$key] = $value;
        } else {
            $this->vars = array_merge($this->vars, $key);
        }

        return $this;
    }

    /**
     * Check if a specified key exists
     * 
     * @param  string $key 
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->vars) || $this->hasShared($key);
    }

    /**
     * Get a specified value
     * 
     * @param  string     $key 
     * @param  mixed|null $default 
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key)
            ? $this->vars[$key]
            : $default;
    }

    /**
     * Render a string
     * 
     * @param  string $template 
     * @param  array|null $params 
     * @return string
     */
    public function renderString($template, array $params = null)
    {
        if (! empty($params)) {
            $this->assign($params);
        }

        $compiled = $this->getCompiler()->compile($template);

        $tempFile = tempnam(sys_get_temp_dir(), __FUNCTION__);

        $this->writeToFile($tempFile, $compiled);

        $template = $this->readBuffer($tempFile);

        unlink($tempFile);

        return $template;
    }

    /**
     * Render a specified view
     * 
     * @param  string     $page 
     * @param  array|null $params 
     * @return mixed
     * 
     * @throws $e
     */
    public function render($page, array $params = null)
    {
        $this->setFile($page);

        try {
            if (! file_exists($file = $this->getFile())) {
                throw new NotFoundException("Template file couldn't be found: " . $file);
            }

            if (! empty($params)) {
                $this->assign($params);
            }
            
            $template = $this->tryCompile($file);
        } catch (Exception $e) {
            $this->flushSections();

            $this->dispatchEvent('exception', $e);

            throw $e;
        } catch (Throwable $e) {
            $this->flushSections();

            $this->dispatchEvent('fatal.error', $e);

            throw $e;
        } finally {
            $this->renderingCompleted(function () {
                $this->dispatchEvent('rendering.completed');

                $this->flushSections();

                $this->onEventEmitterEnabled(function ($emitter) {
                    $emitter->removeAllListeners();
                });

                unset($this->vars, $this->shared, $this->customDirectives); // Free the memory
            });
        }

        return $template;
    }

    /**
     * Compile the specified view to valid PHP
     * 
     * @param  string $file 
     * @return mixed
     */
    private function compile($file)
    {
        $this->dispatchEvent('before.compile', $file);

        $compiled = $this->getCompiler()->compile(file_get_contents($file));

        $this->dispatchEvent('after.compile', $file);

        return $compiled;
    }

    /**
     * Register the default values
     * 
     * @return $this
     */
    protected function assignDefaults()
    {
        $this->assign('__env', $this);
        $this->assign('thisFile', $this->getFile());

        return $this;
    }

    /**
     * Read the buffer of the template file
     * 
     * @param  string $template 
     * @return mixed
     */
    private function readBuffer($template)
    {
        try {
            mb_internal_encoding('UTF-8');

            $this->assignDefaults();

            $this->incrementRendering();

            $obLevel = ob_get_level();

            ob_start();
            
            extract($this->shared + $this->vars, EXTR_OVERWRITE);

            include($template);

            $contents = ob_get_clean();
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
        } catch (Throwable $e) {
            $this->handleViewException(new FatalThrowableError($e), $obLevel);
        }

        $this->decrementRendering();

        $contents = ltrim($contents);

        return $this->getConfig('compress') != false
            ? $this->compressBuffer($contents)
            : $contents;
    }

    /**
     * Compress/minify the given buffer (experimental)
     * 
     * @param  string $buffer 
     * @return string
     */
    private function compressBuffer($buffer)
    {
        if (preg_match("/\<html/i",$buffer) == 1 && preg_match("/\<\/html\>/i",$buffer) == 1) {
            $buffer = preg_replace(
                ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s'],
                ['>', '<', '\\1'],
                $buffer
            );
        }

        return $buffer;
    }

    /**
     * Handle a view exception
     *
     * @param  \Exception $e
     * @param  int        $obLevel
     * @return void
     *
     * @throws $e
     */
    protected function handleViewException(Exception $e, $obLevel)
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }

    /**
     * Flush the cache folder
     * 
     * @return void
     */
    public function flushCache()
    {
        $iterator = new \DirectoryIterator($this->getConfig('cache_folder'));

        foreach ($iterator as $file) {
            if (! $file->isDot()) {
                unlink($file->getPathname());
            }
        }
    }

    /**
     * Compile the template and retrieve the contents
     * 
     * @param  string $tpl 
     * @return mixed
     */
    private function tryCompile($template)
    {
        $cache = $this->getMaskedFileName($template);
        $path = $this->getConfig('cache_folder');

        if (! file_exists($path)) {
            mkdir($path, 0777, true); // recursive path creating
        }

        if (file_exists($cache) && (filemtime($cache) > filemtime($template))) {
            // CACHED
        } else {
            $this->writeToFile(
                $cache,
                $this->compile($template),
                $this->getFile()
            );
        }

        return $this->readBuffer($cache);
    }

    /**
     * Get the full path to the cached template file
     * 
     * @param  string $file 
     * @param  string $format 
     * @return string
     */
    private function getMaskedFileName($file, $format = '.php')
    {
        return $this->getConfig('cache_folder') .  self::cacheFilePrefix . '.' . $this->fileName . strtoupper(substr(md5($file), -19)) . $format;
    }

    /**
     * Write the compiled PHP code to the template file
     * 
     * @param  string $file 
     * @param  string $content 
     * @param  string $origin 
     * @return int
     */
    private function writeToFile($file, $content, $origin = 'n/a')
    {
        return file_put_contents($file, $this->getFileSignature($origin) . $content);
    }

    /**
     * Increment the view rendering
     * 
     * @return $this
     */
    private function incrementRendering()
    {
        $this->renderCounter++;

        $this->dispatchEvent('rendering.incremented', $this->renderCounter);

        return $this;
    }

    /**
     * Decrement the view rendering
     * 
     * @return $this
     */
    private function decrementRendering()
    {
        if ($this->renderCounter > 0) {
            $this->renderCounter--;

            $this->dispatchEvent('rendering.decremented', $this->renderCounter);
        }

        return $this;
    }

    /**
     * Set the current template file name
     * 
     * @param  string $name 
     * @return void
     */
    private function setFile($name)
    {
        $this->fileName = $name . '.';
        $this->file     = $this->getConfig('tpl_folder') . str_replace('.', '/', $name) . $this->getConfig('tpl_file_format', '.pytotpl.php');
    }

    /**
     * Get the current template file name
     * 
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Execute the closure if the view rendering has been completed
     * 
     * @param  \Closure $callback 
     * @return mixed
     */
    private function renderingCompleted(Closure $callback)
    {
        return ($this->renderCounter == 0)
            ? $callback()
            : null;
    }

    /**
     * Set a custom directive handler
     * 
     * @param  string   $name 
     * @param  callable $handler 
     * @return $this
     */
    public function directive($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;

        return $this;
    }

    /**
     * Get the list of custom directives
     *
     * @return array
     */
    public function getCustomDirectives()
    {
        return $this->customDirectives;
    }

    /**
     * Get a specified directive handler
     * 
     * @param  string $name 
     * @return callable
     */
    public function getCustomDirective($name)
    {
        return $this->customDirectives[$name];
    }

    /**
     * Assign a value to a key
     * 
     * @param  string|array $key 
     * @param  mixed|null   $value 
     * @return $this
     */
    public function with($key, $value = null)
    {
        $this->assign($key, $value);

        return $this;
    }

    /**
     * Dynamically assign values
     * 
     * @param  string $method 
     * @param  array  $args 
     * @return $this
     */
    public function __call($method, $args)
    {
        if ($this->isEventEmitterEnabled()) { // ...
            if (method_exists($emitter = $this->getEventEmitter(), $method)) {
                return $emitter->$method(...$args);
            }
        }

        if (! $this->startsWith($method, 'with')) {
            throw new BadMethodCallException("Method [$method] does not exist.");
        }

        return $this->with($this->snakeCase(substr($method, 4)), $args[0]);
    }

    /**
     * Convert a string to snake case
     * 
     * @param  string $key 
     * @return string
     */
    private function snakeCase($key)
    {
        if (! ctype_lower($key)) {
            $key = preg_replace('/\s+/u', '', $key);
            $key = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . '_', $key), 'UTF-8');
        }

        return $key;
    }

    /**
     * Check if a string starts with a specified value
     * 
     * @param  string $haystack 
     * @param  string $needle 
     * @return bool
     */
    private function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    /**
     * Get the compiled file signature
     * 
     * @param  string $origin 
     * @return string
     */
    private function getFileSignature($origin)
    {
        return "<?php " . sprintf(self::blockDirectAccessMSG, get_class($this)) . PHP_EOL . "/*" . PHP_EOL
                        . "[{$origin}] Compiled by PytoTPL Engine (" . date("Y-m-d H:i:s") . ")"
                        . PHP_EOL . "*/"
                        . PHP_EOL . "?>";
    }
}
