<?php

namespace Jagalan\Queue\Test;

use Jagalan\Queue\QueueServiceProvider;

class QueueServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	public function testItProvidesTheMethods()
	{
		$queueServiceProvider = new QueueServiceProvider(new \Illuminate\Foundation\Application());
		$this->assertContains('queue.autolistener', $queueServiceProvider->provides());
		$this->assertContains('command.queue.autolisten', $queueServiceProvider->provides());

		//Just in case, check that we return the original values as well
		$this->assertGreaterThan(2, count($queueServiceProvider->provides()));
	}
}