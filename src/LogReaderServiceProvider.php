<?php

namespace Jackiedo\LogReader;

use Illuminate\Support\ServiceProvider;

class LogReaderServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $config = __DIR__.'/Config/config.php';
        $this->mergeConfigFrom($config, 'log-reader');

        $this->publishes([
            $config => config_path('log-reader.php'),
        ], 'config');

        $this->app->bind('log-reader', function () {
            return new LogReader;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['log-reader'];
    }
}
