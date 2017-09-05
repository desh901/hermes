<?php

namespace Hermes\Core\Console;

use Symfony\Component\Process\ProcessUtils;
use Illuminate\Contracts\Container\Container;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Application as BaseApplication;
use Hermes\Core\Contracts\Console\Application as ApplicationContract;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Application extends BaseApplication implements ApplicationContract
{

    /**
     * The Hermes application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $hermes;

    /**
     * The output from the previous command.
     *
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    protected $lastOutput;

    /**
     * The console application bootstrappers.
     *
     * @var array
     */
    protected static $bootstrappers = [];

    public function __construct(Container $hermes, $version)
    {
        parent::__construct('Hermes Framework', $version);

        $this->hermes = $hermes;
        $this->setAutoExit(false);
        $this->setCatchExceptions(false);

        $this->bootstrap();
    }

    /**
     * Determine the proper PHP executable.
     *
     * @return string
     */
    public static function phpBinary()
    {
        return ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
    }

    /**
     * Determine the proper Hermes executable.
     *
     * @return string
     */
    public static function hermesBinary()
    {
        return defined('HERMES_BINARY') ? ProcessUtils::escapeArgument(HERMES_BINARY) : 'hermes';
    }

    /**
     * Format the given command as a fully-qualified executable command.
     *
     * @param  string  $string
     * @return string
     */
    public static function formatCommandString($string)
    {
        return sprintf('%s %s %s', static::phpBinary(), static::hermesBinary(), $string);
    }

    /**
     * Register a console "starting" bootstrapper.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function starting(\Closure $callback)
    {
        static::$bootstrappers[] = $callback;
    }

    /**
     * Bootstrap the console application.
     *
     * @return void
     */
    protected function bootstrap()
    {
        foreach (static::$bootstrappers as $bootstrapper) {
            $bootstrapper($this);
        }
    }

    /**
     * Run an Hermes console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface  $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        $parameters = collect($parameters)->prepend($command);

        $this->lastOutput = $outputBuffer ?: new BufferedOutput();

        $this->setCatchExceptions(false);

        $result = $this->run(new ArrayInput($parameters->toArray()), $this->lastOutput);

        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * Add a command to the console.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(SymfonyCommand $command)
    {
        if ($command instanceof Command) {
            $command->setHermes($this->hermes);
        }

        return $this->addToParent($command);
    }

    /**
     * Add the command to the parent instance.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function addToParent(SymfonyCommand $command)
    {
        return parent::add($command);
    }

    /**
     * Clear the console application bootstrappers.
     *
     * @return void
     */
    public static function forgetBootstrappers()
    {
        static::$bootstrappers = [];
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        return $this->lastOutput ? $this->lastOutput->fetch() : '';
    }

    /**
     * Add a command, resolving through the application.
     *
     * @param  string  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function resolve($command)
    {
        return $this->add($this->hermes->make($command));
    }

    /**
     * Resolve an array of commands through the application.
     *
     * @param  array|mixed  $commands
     * @return $this
     */
    public function resolveCommands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        foreach ($commands as $command) {
            $this->resolve($command);
        }

        return $this;
    }

}