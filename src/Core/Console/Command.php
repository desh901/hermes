<?php

namespace Hermes\Core\Console;

use Illuminate\Console\Command as IlluminateCommand;

class Command extends IlluminateCommand
{

    /**
     * Application instance
     *
     * @var \Hermes\Core\Contracts\Application
     */
    protected $hermes;

    /**
     * Get the Hermes application instance.
     *
     * @return \Hermes\Core\Contracts\Application
     */
    public function getHermes()
    {
        return $this->hermes;
    }

    /**
     * Set the Hermes application instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $hermes
     * @return void
     */
    public function setHermes($hermes)
    {
        if(!defined('LARAVEL_START'))
            $this->setLaravel($hermes);

        $this->hermes = $hermes;
    }
}
