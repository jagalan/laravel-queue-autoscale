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
	*  Child processes
	*/
	protected $_childs = array();

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
	*	Forks the execution
	*/
	protected function _fork($connection, $queue, $delay, $memory, $timeout, $maxCyclesPerChild)
	{
		$pid = pcntl_fork();
		if ($pid === 0)
		{
		    //Child process
		    $app = $this->app;
		    $childListener = new ChildListener($app['path.base']);
		    $childListener->listen(
				$connection, $queue, $delay, $memory, $timeout, $maxCyclesPerChild
			);
		}
		else
		{
			\Log::info('New child process, pid:'.$pid);
			$this->_childs[$pid] = $pid;
			return $pid;
		}
	}

	/**
	*	Removes the child processes that have terminated from the list 
	*/
	protected function _checkChilds()
	{
		foreach ($this->_childs as $childPid)
		{
			\Log::info('Checking child: '.$childPid);
			$childStatus = pcntl_waitpid($childPid, $status, WNOHANG);
			if ($childStatus === $childPid)
			{
				\Log::info('Child '.$childPid.' has finished');
				unset($this->_childs[$childPid]);
			}
		}
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->setListenerOptions();

		$delay = $this->input->getOption('delay');
		$memory = $this->input->getOption('memory');
		$connection = $this->input->getArgument('connection');
		$timeout = $this->input->getOption('timeout');
		$maxCyclesPerChild = $this->input->getOption('MaxCyclesPerChild');
		$maxChildProcesses= $this->input->getOption('MaxChildProcesses');

		$queue = $this->getQueue($connection);

		// Infinite loop to handle child creation
		while (true)
		{
			$this->_checkChilds();
			if (count($this->_childs) < $maxChildProcesses)
			{
				$this->_fork($connection, $queue, $delay, $memory, $timeout, $maxCyclesPerChild);
			}
			sleep(1);
		}
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), array(
			array('MaxCyclesPerChild', null, InputOption::VALUE_OPTIONAL, 'How many cycles each child will run before dying', 5),
			array('MaxChildProcesses', null, InputOption::VALUE_OPTIONAL, 'Maximum child to execute', 2),
			//array('MaxCyclesPerChild', null, InputOption::VALUE_OPTIONAL, 'How many cycles each child will run before dying', null)
			));
	}
}