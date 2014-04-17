<?php namespace Jagalan\Queue;

use Illuminate\Queue\QueueServiceProvider as BaseProvider;
use Jagalan\Queue\Console\AutoListenCommand;
use Jagalan\Queue\Console\QueueStatusCommand;

/**
 * Class QueueServiceProvider
 */
class QueueServiceProvider extends BaseProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {   
        parent::register();

        $this->registerAutoListenCommand();
    }

    /**
     * Register the queue autolistener console command.
     *
     * @return void
     */
    protected function registerAutoListenCommand()
    {
        $this->app->bindShared('command.queue.autolisten', function($app)
        {
            return new AutoListenCommand($app);
        });

        $this->commands('command.queue.autolisten');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(parent::provides(), array('queue.autolistener', 'command.queue.autolisten'));
    }

}