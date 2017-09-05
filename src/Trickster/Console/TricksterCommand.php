<?php

namespace Hermes\Trickster\Console;


use Psy\Shell;
use Psy\Configuration;
use Hermes\Core\Console\Command;
use Hermes\Trickster\ClassAliasAutoloader;
use Symfony\Component\Console\Input\InputArgument;

class TricksterCommand extends Command
{

    /**
     * Hermes commands to include in the trickster shell
     *
     * @var array
     */

    protected $availableCommands = [
        'hello', 'action'
    ];

    /**
     * The console command name
     *
     * @var string
     */
    protected $name = 'trickster';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Interact with your Hermes application with a shell';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {

        $this->getApplication()->setCatchExceptions(false);

        $config = new Configuration([
            'updateCheck' => 'never',
            'startupMessage' => "<comment><--- HERMES Trickster ---> v{$this->getApplication()->getVersion()}</comment>"
        ]);

        // TODO: add action and objects caster

        $shell = new Shell($config);
        $shell->addCommands($this->getCommands());
        $shell->setIncludes($this->argument('include'));

        if(!defined('LARAVEL_START')) {
            $path = $this->getHermes()->basePath('vendor/composer/autoload_classmap.php');
        }else{
            $path = $this->getLaravel()->basePath('vendor/composer/autoload_classmap.php');
        }

        $loader = ClassAliasAutoloader::register($shell, $path);

        try {
            $shell->run();
        } finally {
            $loader->unregister();
        }

    }

    /**
     * Get hermes commands to pass through PsySH
     *
     * @return array
     */
    protected function getCommands()
    {

        $commands = [];

        foreach ($this->getApplication()->all() as $name => $command) {

            if(in_array($name, $this->availableCommands)) {
                $commands[] = $command;
            }

        }

        return $commands;

    }

    /**
     * Get the console command arguments
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['include', InputArgument::IS_ARRAY, 'Include file(s) before starting trickster']
        ];
    }
}