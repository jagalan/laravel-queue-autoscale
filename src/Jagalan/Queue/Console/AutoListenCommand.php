<?php namespace Jagalan\Queue\Console;

use Illuminate\Queue\Listener as BaseListener;
use Illuminate\Queue\Console\ListenCommand;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AutoListenCommand extends ListenCommand{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'queue:autolisten';

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
	public function __construct(BaseListener $listener)
	{
		parent::__construct($listener);
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
			//$this->setListenerOptions();

			$delay = $this->input->getOption('delay');

			// The memory limit is the amount of memory we will allow the script to occupy
			// before killing it and letting a process manager restart it for us, which
			// is to protect us against any memory leaks that will be in the scripts.
			$memory = $this->input->getOption('memory');

			$connection = $this->input->getArgument('connection');

			$timeout = $this->input->getOption('timeout');

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
			     $this->listener->listen(
					$connection, $queue, $delay, $memory, $timeout
				);
			}
			
			//sleep(5);
		//}
	}
}