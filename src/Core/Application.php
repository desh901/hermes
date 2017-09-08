<?php

namespace Hermes\Core;

use Hermes\Core\Contracts\Parsing\Factory;
use Hermes\Core\Parsing\ParsingManager;
use Hermes\Core\Routing\ActionRouter;
use Hermes\Ink\Context;
use Hermes\Ink\Credentials\CredentialsManager;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Container\BoundMethod;
use Hermes\Core\Contracts\Application as ApplicationContract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class Application extends Container implements ApplicationContract
{

    const VERSION = '1.0.0';

    protected $basePath;

    /**
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * The array of booted callbacks.
     *
     * @var array
     */
    protected $bootedCallbacks = [];

    /**
     * The array of terminating callbacks.
     *
     * @var array
     */
    protected $terminatingCallbacks = [];

    /**
     * Indicates if the application has been bootstrapped before.
     *
     * @var bool
     */
    protected $hasBeenBootstrapped = false;


    /**
     * Create a new Hermes application instance.
     *
     * @param  string|null  $basePath
     */
    public function __construct($basePath = null)
    {

        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();

        $this->registerParsers();

        $this->registerCredentials();

        $this->registerContext();

        $this->registerActionFactory();

        $this->registerCoreContainerAliases();

    }

    /**
     * Set the base path for the application.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('sdk.path', $this->path());
        $this->instance('sdk.path.base', $this->basePath());
        $this->instance('sdk.path.config', $this->configPath());
        $this->instance('sdk.path.lang', $this->langPath());
        //$this->instance('sdk.path.bootstrap', $this->bootstrapPath());
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @param string $path Optionally, a path to append to the app path
     * @return string
     */
    public function path($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'sdk'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @param string $path Optionally, a path to append to the base path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param string $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the lang directory.
     *
     * @param string $path Optionally, a path to append to the lang path
     * @return string
     */
    public function langPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'lang'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }


    /**
     * Get the path to the bootstrap directory.
     *
     * @param string $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'bootstrap'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
        return BoundMethod::call($this, $callback, $parameters, $defaultMethod);
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('hermes', $this);

        $this->instance(Container::class, $this);

        $this->registerConfigurationService();

        $this->registerFilesystemService();

        $this->registerTranslationService();

        $this->registerValidationService();

        $this->registerActionRouterService();

        $this->registerCacheService();

    }

    /**
     * Register the action router
     *
     * @return void
     */
    protected function registerActionRouterService()
    {

        $this->singleton('router', function($app) {
            return new ActionRouter($app);
        });

        $this->booted(function($app) {
            $app['router']->getRoutes()->refreshNameLookups();
        });

    }

    /**
     * Loads the configuration file
     *
     * @return void
     */
    protected function registerConfigurationService()
    {

        $items = [
            'cache'  => require $this->configPath('cache.php'),
            'hermes' => require $this->configPath('hermes.php')
        ];

        $this->instance('config', new Repository($items));

    }

    /**
     * Loads the filesystem service
     *
     * @return void
     */
    protected function registerFilesystemService()
    {

        $this->singleton('files', function() {
            return new Filesystem();
        });

    }

    /**
     * Register the action router
     *
     * @return void
     */
    protected function registerCacheService()
    {

        $this->singleton('cache', function($app) {
            return new CacheManager($app);
        });

        $this->singleton('cache.store', function($app) {
            return $app['cache']->driver();
        });

    }

    /**
     * Register the validation service
     *
     * @return void
     */
    protected function registerValidationService()
    {

        $this->singleton('validator', function($app) {
            return new \Illuminate\Validation\Factory($app['translator'], $app);
        });

    }

    /**
     * Register the translation service
     *
     * @return void
     */
    protected function registerTranslationService()
    {

        $this->singleton('translation.loader', function($app) {

            return new FileLoader($app['files'], $app['sdk.path.lang']);

        });

        $this->singleton('translator', function($app) {

            $locale = $app['config']['hermes.locale'];
            $fallback = $app['sdk.path.lang'];

            $translator = new Translator($app['translation.loader'], $locale);

            $translator->setFallback($fallback);

            return $translator;
        });

    }


    /**
     * Register the basic request parsers.
     *
     * @return void
     */
    protected function registerParsers()
    {

        $this->singleton('parsing', function($app) {
            return new ParsingManager($app);
        });

        $this->singleton('parsing.parser', function($app) {
            return $app['parsing']->driver();
        });

        $this->alias('parsing', Factory::class);

    }

    /**
     * Register the credentials drivers.
     *
     * @return void
     */
    protected function registerCredentials()
    {

        $this->singleton('credentials', function($app) {
            return new CredentialsManager($app);
        });

        $this->singleton('credentials.driver', function($app) {
            $mode = $app['config']['hermes.mode'];
            $type = $app['config']['hermes'][$mode]['credentials']['type'];

            return $app['credentials']->driver($type);
        });

    }

    /**
     * Register sdk context
     *
     * @return void
     */
    protected function registerContext()
    {

        $this->singleton('context', function($app) {

            return new Context($app['config']['hermes'], $app['cache.store'], $app['credentials.driver']);

        });

        $this->alias('context', \Hermes\Ink\Contracts\Context::class);

    }

    /**
     *
     * Registers the actions factory
     *
     * @return void
     */
    protected function registerActionFactory()
    {

        $this->singleton('factory', function($app) {

            return new \Hermes\Ink\Factory($app['context'], $app['router']);

        });

        $this->alias('factory', \Hermes\Core\Contracts\Factory::class);

    }

    /**
     * Resolve the given type from the container.
     *
     * (Overriding Container::make)
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        $abstract = $this->getAlias($abstract);

        return parent::make($abstract);
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {

        $aliases = [
            'hermes'            => [Application::class, \Illuminate\Contracts\Container\Container::class, ApplicationContract::class],
            'router'            => [ActionRouter::class],
            'cache'             => [CacheManager::class, \Illuminate\Contracts\Cache\Factory::class],
            'cache.store'       => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class],
            'config'            => [Repository::class, \Illuminate\Contracts\Config\Repository::class],
            'validator'         => [\Illuminate\Validation\Factory::class, Validator::class, \Illuminate\Contracts\Validation\Factory::class]
        ];

        foreach ($aliases as $key => $nestedAliases) {
            foreach ($nestedAliases as $alias) {
                $this->alias($key, $alias);
            }
        }

    }

    /**
     * Register a new "booted" listener.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) {
            $this->fireAppCallbacks([$callback]);
        }
    }

    /**
     * Call the booting callbacks for the application.
     *
     * @param  array  $callbacks
     * @return void
     */
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Run the given array of bootstrap classes.
     *
     * @param  array  $bootstrappers
     * @return void
     */
    public function bootstrapWith(array $bootstrappers)
    {
        $this->hasBeenBootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this['events']->fire('bootstrapping: '.$bootstrapper, [$this]);

            $this->make($bootstrapper)->bootstrap($this);

            $this['events']->fire('bootstrapped: '.$bootstrapper, [$this]);
        }
    }

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenBootstrapped()
    {
        return $this->hasBeenBootstrapped;
    }

    /**
     * Terminate the application.
     *
     * @return void
     */
    public function terminate()
    {
        foreach ($this->terminatingCallbacks as $terminating) {
            $this->call($terminating);
        }
    }





}