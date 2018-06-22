<?php

namespace FcPhp\Cache\Traits
{
	trait CacheTrait
	{
		/**
		 * Method to write cache
		 *
		 * @param string $key Key to name cache
		 * @param mixed $content Content to cache
		 * @param int $ttl time to live cache
		 * @return void
		 */
		private function write(string $key, string $content, int $ttl) :void
		{
			$content = time() + $ttl . '|' . base64_encode($content);
			if($this->isRedis()) {
				$this->redis->set(self::CACHE_REDIS_ALIAS . $key, $content);
			}else{
				$this->fmake($key, $content);
			}
		}

		/**
		 * Method to read cache
		 *
		 * @param string $key Key to name cache
		 * @return mixed
		 */
		private function read(string $key)
		{
			if($this->isRedis()) {
				return $this->redis->get(self::CACHE_REDIS_ALIAS . $key);
			}else{
				return $this->fread($key);
			}
		}
	}
}