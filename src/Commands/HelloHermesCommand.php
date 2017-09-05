<?php

namespace Hermes\Commands;


use Hermes\Core\Console\Command;

class HelloHermesCommand extends Command
{

    protected $signature = 'hello';

    protected $description = 'Say hello to Hermes!';

    public function handle()
    {

        $this->info('Hello Hermes!');

    }

}