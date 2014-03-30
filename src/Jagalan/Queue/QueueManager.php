<?php
namespace Jagalan\Queue;

use Illuminate\Queue\QueueManager as BaseManager;

class QueueManager extends BaseManager
{
	const COUNT_CACHE_KEY = 'Jagalan_Queue_Counter';

	/**
	 * Dynamically pass calls to the default connection.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		try
		{
			switch ($method)
			{
				case 'push':
				case 'later':
					\Cache::increment(self::COUNT_CACHE_KEY);
				break;
				case 'pop':
					\Cache::decrement(self::COUNT_CACHE_KEY);
				break;

			}
		} catch (\LogicException $e) {}

		return parent::__call($method, $parameters);
	}
}