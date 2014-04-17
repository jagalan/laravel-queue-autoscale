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
        $this->registerStatusQueueCommand();
    }

    /**
     * Register the queue manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $me = $this;

        $this->app->bindShared('queue', function($app) use ($me)
        {
            // Once we have an instance of the queue manager, we will register the various
            // resolvers for the queue connectors. These connectors are responsible for
            // creating the classes that accept queue configs and instantiate queues.
            $manager = new QueueManager($app);

            $me->registerConnectors($manager);

            return $manager;
        });
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
     * Register the queue status console command.
     *
     * @return void
     */
    protected function registerStatusQueueCommand()
    {
        $this->app->bindShared('command.queue.status', function($app)
        {
            return new QueueStatusCommand($app);
        });

        $this->commands('command.queue.status');
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