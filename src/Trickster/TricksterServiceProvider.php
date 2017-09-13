<?php

namespace Hermes\Trickster;


use Hermes\Trickster\Console\TricksterCommand;
use Illuminate\Support\ServiceProvider;

class TricksterServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register the provider
     */
    public function register()
    {

        $this->app->singleton('command.trickster', function() {
            return new TricksterCommand();
        });

        $this->commands(['command.trickster']);

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.trickster'];
    }

}