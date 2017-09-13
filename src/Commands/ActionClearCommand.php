<?php

namespace Hermes\Commands;

use Hermes\Core\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ActionClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'action:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the action cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new action clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->files->delete($this->hermes->getCachedActionsPath());

        $this->info('Action cache cleared!');
    }
}
