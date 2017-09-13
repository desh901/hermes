<?php

namespace Hermes\Core;

use Hermes\Core\Console\Application as Hermes;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * Register the package's custom Artisan commands.
     *
     * @param  array|mixed  $commands
     * @return void
     */
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        Hermes::starting(function ($hermes) use ($commands) {
            $hermes->resolveCommands($commands);
        });
    }

}