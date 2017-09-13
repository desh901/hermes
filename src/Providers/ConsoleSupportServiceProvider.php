<?php

namespace Hermes\Providers;


use Hermes\Core\ServiceProvider;
use Hermes\Commands\ServeCommand;
use Hermes\Commands\CacheClearCommand;
use Hermes\Commands\ConfigCacheCommand;
use Hermes\Commands\ConfigClearCommand;
use Hermes\Commands\ClearCompiledCommand;
use Hermes\Trickster\Console\TricksterCommand;

class ConsoleSupportServiceProvider extends ServiceProvider
{

    /**
     * The commands provided by this service
     *
     * @var array
     */
    protected $commands = [
        ClearCompiledCommand::class,
        ConfigClearCommand::class,
        ConfigCacheCommand::class,
        CacheClearCommand::class,
        TricksterCommand::class,
        ServeCommand::class,
    ];

    /**
     * Registers the provider
     *
     */
    public function register()
    {

        $this->commands($this->commands);

    }

}