<?php namespace Jackiedo\LogReader;

use Illuminate\Support\ServiceProvider;
use Jackiedo\LogReader\Console\Commands\LogReaderDeleteCommand;
use Jackiedo\LogReader\Console\Commands\LogReaderDetailCommand;
use Jackiedo\LogReader\Console\Commands\LogReaderFileListCommand;
use Jackiedo\LogReader\Console\Commands\LogReaderGetCommand;
use Jackiedo\LogReader\Console\Commands\LogReaderRemoveFileCommand;

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

        $this->registerCommands();
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

    /**
     * Register commands
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app->bindShared('command.log-reader.delete', function($app) {
            return new LogReaderDeleteCommand($app['log-reader']);
        });

        $this->app->bindShared('command.log-reader.detail', function($app) {
            return new LogReaderDetailCommand($app['log-reader']);
        });

        $this->app->bindShared('command.log-reader.file-list', function($app) {
            return new LogReaderFileListCommand($app['log-reader']);
        });

        $this->app->bindShared('command.log-reader.get', function($app) {
            return new LogReaderGetCommand($app['log-reader']);
        });

        $this->app->bindShared('command.log-reader.remove-file', function($app) {
            return new LogReaderRemoveFileCommand($app['log-reader']);
        });

        $this->commands('command.log-reader.delete');
        $this->commands('command.log-reader.detail');
        $this->commands('command.log-reader.file-list');
        $this->commands('command.log-reader.get');
        $this->commands('command.log-reader.remove-file');
    }
}
