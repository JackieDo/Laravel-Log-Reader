<?php namespace Jackiedo\LogReader;

use Illuminate\Support\ServiceProvider;

/**
 * LogReaderServiceProvider
 *
 * @package Jackiedo\LogReader
 * @author Jackie Do <anhvudo@gmail.com>
 * @copyright 2017
 * @access public
 */
class LogReaderServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Publishing package's config
         */
        $packageConfigPath = __DIR__ . '/../../config/config.php';
        $config            = config_path('log-reader.php');

        $this->publishes([
            $packageConfigPath => $config,
        ], 'config');

        if (file_exists($config)) {
            $this->mergeConfigFrom($packageConfigPath, 'log-reader');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('log-reader', function ($app) {
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