<?php

namespace Jagalan\Queue;

use Illuminate\Queue\Listener as BaseListener;

class ChildListener extends BaseListener
{
	/*
	* Controls the maximum number of jobs the listener will process
	*
	* Unlimited by default
	*/
	protected $_executions = -1;

	/**
	* Sets the maximum number of executions
	* @param  int  $maxExecutions
	*/
	public function setMaxExecutions($maxExecutions)
	{
		$this->_executions = $maxExecutions;
	}

	protected function _maxExecutionsExceeded()
	{
		if ($this->_executions === -1) return false;
		return (--$this->_executions === 0);
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
		if ($this->_maxExecutionsExceeded())
		{
			$this->stop();
		}
	}
}