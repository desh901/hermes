<?php

namespace Hermes\Commands;


class ObjectMakeCommand extends GeneratorCommand
{

    /**
     * The console command name
     *
     * @var string
     */

    protected $name = 'make:object';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Ink object class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Object';

    /**
     * Execute the console command.
     */
    public function fire()
    {

        parent::fire();

    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/object.stub';
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