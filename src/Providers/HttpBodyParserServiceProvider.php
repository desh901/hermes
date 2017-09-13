<?php

namespace Hermes\Providers;


use Hermes\Core\Parsing\ParsingManager;
use Illuminate\Support\ServiceProvider;
use Hermes\Core\Contracts\Parsing\Factory;

class HttpBodyParserServiceProvider extends ServiceProvider
{

    /**
     * Registers the provider
     */
    public function register()
    {
        $this->app->singleton('hermes.parsing', function($app) {
            return new ParsingManager($app);
        });

        $this->app->singleton('hermes.parsing.parser', function($app) {
            return $app['parsing']->driver();
        });

        $this->app->alias('hermes.parsing', Factory::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hermes.parsing', 'hermes.parsing.parser'];
    }

}