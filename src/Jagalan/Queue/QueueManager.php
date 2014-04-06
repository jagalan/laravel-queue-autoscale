<?php
namespace Jagalan\Queue;

use Illuminate\Queue\QueueManager as BaseManager;

class QueueManager extends BaseManager
{
	const COUNT_CACHE_KEY = 'Jagalan_Queue_Counter';

	public function __construct($app)
	{
		return parent::__construct($app);
	}

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
			if (!\Cache::has(self::COUNT_CACHE_KEY)) \Cache::forever(self::COUNT_CACHE_KEY, 0);
			\Log::info($method);
			switch ($method)
			{
				case 'push':
				case 'later':
					\Cache::increment(self::COUNT_CACHE_KEY);
				break;
				//case 'pop':
				//	\Cache::decrement(self::COUNT_CACHE_KEY);
				//break;
				default:
			}
		} catch (\LogicException $e) {}

		return parent::__call($method, $parameters);
	}
}