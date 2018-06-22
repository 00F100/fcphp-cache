<?php

namespace FcPhp\Cache\Traits
{
	trait CacheRedisTrait
	{
		/**
		 * Method to verify if Cache use Redis
		 *
		 * @return bool
		 */
		private function isRedis()
		{
			return $this->strategy == 'redis';
		}

		/**
		 * Method to clean Redis old caches
		 *
		 * @return void
		 */
		private function redisClean() :void
		{
			$list = $this->redis->keys('*');
			if(is_array($list)) {
				foreach($list as $key) {
					if(substr($key, 0, strlen(self::CACHE_REDIS_ALIAS)) == self::CACHE_REDIS_ALIAS) {
						$content = $this->redis->get($key);
						$content = explode('|', $content);
						$time = current($content);
						if($time < time()) {
							$this->delete($key);
						}
					}
				}
			}
		}
	}
}