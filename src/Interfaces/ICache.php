<?php

namespace FcPhp\Cache\Interfaces
{
	use FcPhp\Cache\Interfaces\ICache;
	use FcPhp\Redis\Interfaces\IRedis;
	
	interface ICache
	{
		public function __construct(?IRedis $redis, string $path = null);

		public function set(string $key, $content, int $ttl) :ICache;

		public function has(string $key) :bool;

		public function delete(string $key) :void;

		public function get(string $key);
	}
}