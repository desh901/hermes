<?php

namespace Hermes\Providers;


use Hermes\Core\Routing\ActionRouter;
use Illuminate\Support\ServiceProvider;

class ActionRouterServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider
     *
     */
    public function register()
    {
        $this->app->singleton('hermes.router', function ($app) {
            return new ActionRouter($app);
        });

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

}