<?php

namespace Hermes\Providers;


use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends \Illuminate\Translation\TranslationServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['hermes.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['hermes.fallback_locale']);

            return $trans;
        });
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app['sdk.path.lang']);
        });
    }

}