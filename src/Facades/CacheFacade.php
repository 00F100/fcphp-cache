<?php

namespace FcPhp\Cache\Facades
{
	use FcPhp\Cache\Cache;
	use FcPhp\Cache\Interfaces\ICache;
	use FcPhp\Redis\Facades\RedisFacade;

	class CacheFacade
	{

		/**
		 * @var FcPhp\Cache\Interfaces\ICache
		 */
		public static $instance;

		/**
		 * Method to create new instance of Cache
		 *
		 * @param array $redis Configuration of redis
		 * @param string $path Path to cache in file
		 * @return void
		 */
		public static function getInstance(?array $redis, string $path = null)
		{
			if(!self::$instance instanceof ICache) {
				if(is_array($redis) && isset($redis['host']) && $redis = self::sanitizeRedis($redis)) {
					self::$instance = new Cache(RedisFacade::getInstance($redis['host'], $redis['port'], $redis['password'], $redis['timeout']), null);
				}else{
					self::$instance = new Cache(null, $path);
				}
			}
			return self::$instance;
		}

		/**
		 * Method to reset instance
		 *
		 * @return void
		 */
		public static function reset() :void
		{
			self::$instance = null;
		}

		/**
		 * Method to sanitize array of redis configuration
		 *
		 * @param array $redis Configuration of redis
		 * @return array
		 */
		private static function sanitizeRedis(array &$redis) :array
		{
			$default = [
				'host' => '',
				'port' => '6379',
				'password' => null,
				'timeout' => 100,
			];
			return array_merge($default, $redis);
		}
	}
}