<?php namespace Jagalan\Queue\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class QueueStatusCommand extends Command{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'queue:status';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Provide the number of items in a queue';

	/**
	 * Create a new queue listen command.
	 *
	 * @param  \Illuminate\Queue\Listener  $listener
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		echo \Cache::get(\Jagalan\Queue\QueueManager::COUNT_CACHE_KEY);
	}

}