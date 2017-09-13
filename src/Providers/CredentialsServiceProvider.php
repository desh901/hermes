<?php

namespace Hermes\Providers;


use Hermes\Ink\Credentials\CredentialsManager;
use Illuminate\Support\ServiceProvider;

class CredentialsServiceProvider extends ServiceProvider
{

    /**
     * Registers the provider
     */
    public function register()
    {
        $this->app->singleton('hermes.credentials', function($app) {
            return new CredentialsManager($app);
        });

        $this->app->singleton('hermes.credentials.driver', function($app) {
            $mode = $app['config']['hermes.mode'];
            $type = $app['config']['hermes'][$mode]['credentials']['type'];

            return $app['hermes.credentials']->driver($type);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hermes.credentials', 'hermes.credentials.driver'];
    }

}