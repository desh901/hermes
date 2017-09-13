<?php

namespace Hermes\Core\Console;

use Hermes\Commands\ServeCommand;
use Hermes\Commands\HelloHermesCommand;
use Hermes\Core\Bootstrap\BootProviders;
use Illuminate\Console\Scheduling\Schedule;
use Hermes\Core\Bootstrap\RegisterProviders;
use Hermes\Core\Bootstrap\LoadConfiguration;
use Hermes\Trickster\Console\TricksterCommand;
use Hermes\Core\Console\Application as Hermes;
use Hermes\Core\Contracts\Console\Kernel as KernelContract;

class Kernel implements KernelContract
{

    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The event dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The Hermes application instance.
     *
     * @var Hermes
     */
    protected $hermes;

    /**
     * The Artisan commands provided by the application.
     *
     * @var array
     */
    protected $commands = [
        HelloHermesCommand::class,
        TricksterCommand::class,
        ServeCommand::class
    ];

    /**
     * Indicates if the Closure commands have been loaded.
     *
     * @var bool
     */
    protected $commandsLoaded = false;

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        LoadConfiguration::class,
        RegisterProviders::class,
        BootProviders::class
    ];

    /**
     * Create a new console kernel instance.
     *
     * @param  \Hermes\Core\Contracts\Application  $app
     */
    public function __construct(\Hermes\Core\Contracts\Application $app)
    {
        if (! defined('HERMES_BINARY')) {
            define('HERMES_BINARY', 'hermes');
        }

        $this->app = $app;
    }

    /**
     * Run the console application.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     * @throws \Exception|\Throwable
     */
    public function handle($input, $output = null)
    {
        try {
            $this->bootstrap();

            if (! $this->commandsLoaded) {
                $this->commands();

                $this->commandsLoaded = true;
            }

            return $this->getHermes()->run($input, $output);
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * Terminate the application.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  int  $status
     * @return void
     */
    public function terminate($input, $status)
    {
        $this->app->terminate();
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        //
    }

    /**
     * Register a Closure based command with the application.
     *
     * @param  string  $signature
     * @param  \Closure  $callback
     * @return ClosureCommand
     */
    public function command($signature, \Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        Hermes::starting(function ($artisan) use ($command) {
            $artisan->add($command);
        });

        return $command;
    }

    /**
     * Register the given command with the console application.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return void
     */
    public function registerCommand($command)
    {
        $this->getHermes()->add($command);
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface  $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        $this->bootstrap();

        if (! $this->commandsLoaded) {
            $this->commands();

            $this->commandsLoaded = true;
        }

        return $this->getHermes()->call($command, $parameters, $outputBuffer);
    }

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all()
    {
        $this->bootstrap();

        return $this->getHermes()->all();
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        $this->bootstrap();

        return $this->getHermes()->output();
    }

    /**
     * Bootstrap the application for artisan commands.
     *
     * @return void
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }

        // If we are calling an arbitrary command from within the application, we'll load
        // all of the available deferred providers which will make all of the commands
        // available to an application. Otherwise the command will not be available.
        $this->app->loadDeferredProviders();
    }

    /**
     * Get the Hermes application instance.
     *
     * @return \Hermes\Core\Console\Application
     */
    protected function getHermes()
    {
        if (is_null($this->hermes)) {
            return $this->hermes = (new Hermes($this->app, $this->app->version()))
                ->resolveCommands($this->commands);
        }

        return $this->hermes;
    }

    /**
     * Set the Artisan application instance.
     *
     * @param  \Hermes\Core\Console\Application  $hermes
     * @return void
     */
    public function setHermes($hermes)
    {
        $this->hermes = $hermes;
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

}