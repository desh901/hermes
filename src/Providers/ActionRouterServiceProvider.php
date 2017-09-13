<?php

namespace Hermes\Providers;


use Hermes\Core\Routing\ActionRouter;
use Illuminate\Support\ServiceProvider;
use Hermes\Core\Contracts\Routing\Router;

class ActionRouterServiceProvider extends ServiceProvider
{

    /**
     * Load the provider in a deferred manner
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if($this->app->actionsAreCached()) {
            $this->loadCachedActions();
        } else {
            $this->loadActions();

            $this->app->booted(function() {
                $this->app['hermes.router']->getActions()->refreshNameLookups();
                $this->app['hermes.router']->getActions()->refreshActionLookups();
            });
        }

    }

    /**
     * Load the cached actions for the application.
     *
     * @return void
     */
    protected function loadCachedActions()
    {

        $this->app->booted(function() {
            require $this->app->getCachedActionsPath();
        });

    }

    /**
     * Load the cached actions for the application.
     *
     * @return void
     */
    protected function loadActions()
    {

        if(method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }

    }

    /**
     * Register the service provider
     *
     */
    public function register()
    {
        $this->app->singleton('hermes.router', function ($app) {
            return new ActionRouter($app);
        });

        $this->app->alias('hermes.router', Router::class);

        $this->registerFactory();

    }

    /**
     * Register the action factory
     */
    protected function registerFactory()
    {
        $this->app->singleton('hermes.factory', function($app) {

            return new \Hermes\Ink\Factory($app['hermes.context'], $app['hermes.router']);

        });

        $this->app->alias('hermes.factory', \Hermes\Core\Contracts\Factory::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hermes.router', 'hermes.factory'];
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {

        return call_user_func_array(
            [$this->app->make(Router::class), $method], $parameters
        );

    }

}