<?php

namespace FcPhp\Cache\Facades
{
	use FcPhp\Cache\Cache;
	use FcPhp\Cache\Interfaces\ICache;
	use FcPhp\Redis\Facades\RedisFacade;
	use FcPhp\Crypto\Crypto;

	class CacheFacade
	{

		/**
		 * @var FcPhp\Cache\Interfaces\ICache
		 */
		public static $instance;

		/**
		 * Method to create new instance of Cache
		 *
		 * @param string|array $cacheRepository Configuration of redis or path of dir to cache files
		 * @return FcPhp\Cache\Interfaces\ICache
		 */
		public static function getInstance($cacheRepository, string $nonce = null, string $pathKeys = null) :ICache
		{
			if(!self::$instance instanceof ICache) {
				$crypto =  (!empty($nonce) ? new Crypto($nonce) : null);
				if(is_array($cacheRepository) && isset($cacheRepository['host']) && $redis = self::sanitizeRedis($cacheRepository)) {
					self::$instance = new Cache(RedisFacade::getInstance($redis['host'], $redis['port'], $redis['password'], $redis['timeout']), null, $crypto, $pathKeys);
				}else{
					self::$instance = new Cache(null, $cacheRepository, $crypto, $pathKeys);
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