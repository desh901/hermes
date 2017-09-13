<?php

namespace Hermes\Commands;


use Hermes\Core\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Hermes\Core\Contracts\Console\Kernel;
use Hermes\Core\Routing\ActionCollection;

class ActionCacheCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'action:cache';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Create an action cache file for faster action registration.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new action command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {

        parent::__construct();
        $this->files = $files;

    }

    /**
     * Execute the console command.
     */
    public function fire()
    {

        $this->call('action:clear');

        $actions = $this->getFreshApplicationActions();

        if(count($actions) == 0) {
            $this->error("Your application doesn't have any actions.");
            return;
        }

        foreach ($actions as $action)
        {

            $action->prepareForSerialization();

        }

        $this->files->put(
            $this->hermes->getCachedActionsPath(), $this->buildActionCacheFile($actions)
        );

        $this->info('Actions cached successfully!');

    }

    /**
     * Boot a fresh copy of the application and get the actions.
     *
     * @return \Hermes\Core\Routing\ActionCollection
     */
    protected function getFreshApplicationActions()
    {

        return tap($this->getFreshApplication()['hermes.router']->getActions(), function($actions) {

            $actions->refreshNameLookups();
            $actions->refreshActionLookups();

        });

    }

    /**
     * Get a fresh application instance.
     *
     * @return \Hermes\Core\Application
     */
    protected function getFreshApplication()
    {

        $app = require $this->hermes->bootstrapPath().'/app.php';

        return tap($app, function($app) {
            $app->make(Kernel::class)->bootstrap();
        });

    }

    /**
     * Build the action cache file.
     *
     * @param \Hermes\Core\Routing\ActionCollection $actions
     *
     * @return string
     */
    protected function buildActionCacheFile(ActionCollection $actions)
    {

        $stub = $this->files->get(__DIR__.'/stubs/actions.stub');

        return str_replace('{{actions}}', base64_encode(serialize($actions)), $stub);
    }

}