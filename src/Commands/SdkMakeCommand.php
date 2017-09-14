<?php

namespace Hermes\Commands;


use Illuminate\Support\Str;

class SdkMakeCommand extends GeneratorCommand
{

    /**
     * The console command name
     *
     * @var string
     */

    protected $name = 'make:sdk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new SDK application class';

    /**
     * Execute the console command.
     */
    public function fire()
    {

        parent::fire();


        //TODO: export configuration file
    }

    /**
     * Export the configuration file
     */
    protected function exportConfig($stubName)
    {

        $stub = $this->files->get(__DIR__.'/stubs/'.$stubName.'.stub');

        $className = basename($this->qualifyClass($this->getNameInput()));
        $configFileName = Str::snake($className) . '.php';

        $filePath = $this->hermes->configPath($configFileName);

        if(!$this->files->exists($filePath)){

            $this->files->put(
                $filePath,
                $stub
            );

            $this->info("Created config file $filePath");
        }

    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/sdk.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

}