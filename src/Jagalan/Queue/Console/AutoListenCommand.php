<?php namespace Jagalan\Queue\Console;

use Illuminate\Queue\Listener as BaseListener;
use Illuminate\Queue\Console\ListenCommand;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Jagalan\Queue\ChildListener;

class AutoListenCommand extends ListenCommand{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'queue:autolisten';

	protected $app = null;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Listen to a given queue,launching more listeners if needed';

	/**
	 * Create a new queue listen command.
	 *
	 * @param  \Illuminate\Queue\Listener  $listener
	 * @return void
	 */
	public function __construct($app)
	{
		$this->_app = $app;
		parent::__construct($app['queue.listener']);
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		//while(true)
		//{
			$this->setListenerOptions();

			$delay = $this->input->getOption('delay');

			// The memory limit is the amount of memory we will allow the script to occupy
			// before killing it and letting a process manager restart it for us, which
			// is to protect us against any memory leaks that will be in the scripts.
			$memory = $this->input->getOption('memory');

			$connection = $this->input->getArgument('connection');

			$timeout = $this->input->getOption('timeout');

			$MaxCyclesPerChild = $this->input->getOption('MaxCyclesPerChild');

			// We need to get the right queue for the connection which is set in the queue
			// configuration file for the application. We will pull it based on the set
			// connection being run for the queue operation currently being executed.
			$queue = $this->getQueue($connection);

			$pid = pcntl_fork();
			if ($pid == -1) {
			     die('could not fork');
			} else if ($pid) {
			     // we are the parent
			     $this->listener->listen(
					$connection, $queue, $delay, $memory, $timeout
				);
			} else {
			    //Child process
			    $app = $this->app;
			    $childListener = new ChildListener($app['path.base']);
			    $childListener->listen(
					$connection, $queue, $delay, $memory, $timeout, $MaxCyclesPerChild
				);
			}
			
			//sleep(5);
		//}
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), array(array('MaxCyclesPerChild', null, InputOption::VALUE_OPTIONAL, 'How many cycles each child will run before dying', null)));
	}
}