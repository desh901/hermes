<?php

namespace Hermes\Core\Sdk;

use Exception;
use Hermes\Core\Bootstrap\BootProviders;
use Hermes\Core\Bootstrap\LoadConfiguration;
use Hermes\Core\Bootstrap\RegisterProviders;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Hermes\Core\Contracts\Sdk\Kernel as KernelContract;

class Kernel implements KernelContract
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        LoadConfiguration::class,
        RegisterProviders::class,
        BootProviders::class,
    ];

    /**
     * Create a new SDK kernel instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception  $e
     * @return void
     */
    /*protected function reportException(Exception $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }*/

    /**
     * Render the exception to a response.
     *
     * @param  \Hermes\Ink\Action  $action
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    /*protected function renderException($action, Exception $e)
    {
        return $this->app[ExceptionHandler::class]->render($action, $e);
    }*/

    /**
     * Get the Laravel application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication()
    {
        return $this->app;
    }
}
