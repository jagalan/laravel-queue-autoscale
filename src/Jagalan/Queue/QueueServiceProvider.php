<?php namespace Jagalan\Queue;

use Illuminate\Queue\QueueServiceProvider as BaseProvider;

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
        \Log::info('registering jagalan');
        return parent::register();
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

}