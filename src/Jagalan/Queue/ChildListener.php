<?php

namespace Jagalan\Queue;

use Illuminate\Queue\Listener as BaseListener;

class ChildListener extends BaseListener
{
	/*
	* Controls the maximum number of jobs the listener will process
	*/
	protected $_executions = 100;

	/**
	 * Listen to the given queue connection.
	 *
	 * @param  string  $connection
	 * @param  string  $queue
	 * @param  string  $delay
	 * @param  string  $memory
	 * @param  int     $timeout
	 * @param  int     $maxExecutions
	 * @return void
	 */
	public function listen($connection, $queue, $delay, $memory, $timeout = 60, $maxExecutions = 100)
	{
		$this->_executions = $maxExecutions;
		return parent::listen($connection, $queue, $delay, $memory, $timeout);
	}

	/**
	 * Run the given process.
	 *
	 * @param  \Symfony\Component\Process\Process  $process
	 * @param  int  $memory
	 * @return void
	 */
	public function runProcess(\Symfony\Component\Process\Process $process, $memory)
	{
		parent::runProcess($process, $memory);
		if (--$this->_executions === 0) 
		{
			exit;
		}
	}
}