<?php

namespace Hermes\Commands;


use Hermes\Core\Console\Command;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;

class ServeCommand extends Command
{

    /**
     * The console command name.
     *
     * @string
     */
    protected $signature = 'serve
                            {host=localhost : The host address to serve the application on.} 
                            {port=8000 : The port to serve the application on.}';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws  \Exception
     */
    public function handle()
    {

        chdir($this->hermes->basePath('server'));

        $host = $this->argument('host');

        $port = $this->argument('port');

        $base = $this->hermes->basePath();

        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder())->find(false));

        $this->info("Hermes development server started on http://{$host}:{$port}/");

        if(file_exists("{$base}/server.php")) {
            passthru("{$binary} -S {$host}:{$port} {$base}/server.php");
        }else {
            passthru("{$binary} -S {$host}:{$port} -t {$base}/");
        }

    }

}