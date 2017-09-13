<?php

namespace Hermes\Providers;


use Hermes\Ink\Context;
use Illuminate\Support\ServiceProvider;

class ContextServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Registers the provider
     */
    public function register()
    {
        $this->app->singleton('hermes.context', function($app) {

            return new Context($app['config']['hermes'], $app['cache.store'], $app['hermes.credentials.driver']);

        });

        $this->app->alias('hermes.context', \Hermes\Ink\Contracts\Context::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hermes.context'];
    }

}