<?php namespace Jackiedo\LogReader;

use Illuminate\Support\ServiceProvider;

/**
 * The LogReaderServiceProvider class.
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
        $this->package('jackiedo/log-reader');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['log-reader'] = $this->app->share(function ($app) {
            return new LogReader($app['cache'], $app['config'], $app['request'], $app['paginator']);
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
