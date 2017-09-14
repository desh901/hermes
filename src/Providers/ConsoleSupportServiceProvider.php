<?php

namespace Hermes\Providers;


use Hermes\Commands\ActionCacheCommand;
use Hermes\Commands\ActionClearCommand;
use Hermes\Commands\ActionMakeCommand;
use Hermes\Commands\ObjectMakeCommand;
use Hermes\Commands\SdkMakeCommand;
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
        ActionCacheCommand::class,
        ActionClearCommand::class,
        CacheClearCommand::class,
        ActionMakeCommand::class,
        ObjectMakeCommand::class,
        TricksterCommand::class,
        SdkMakeCommand::class,
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