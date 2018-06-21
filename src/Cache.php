<?php

namespace FcPhp\Cache
{
	use FcPhp\Cache\Interfaces\ICache;
	use FcPhp\Redis\Interfaces\IRedis;

	class Cache implements ICache
	{

		/**
		 * @var string
		 */
		private $strategy = 'redis';

		/**
		 * @var FcPhp\Redis\Interfaces\IRedis
		 */
		private $redis = null;

		/**
		 * @var string
		 */
		private $path = null;

		/**
		 * Method to construct new instance of Cache
		 *
		 * @param FcPhp\Redis\Interfaces\IRedis $redis Redis instance
		 * @param string $path Path to cache in file
		 * @return void
		 */
		public function __construct(?IRedis $redis, string $path = null)
		{
			if($redis instanceof IRedis) {
				$this->redis = $redis;
			}else{
				$this->path = $path;
				$this->strategy = 'path';
			}
		}

		/**
		 * Method to create new cache
		 *
		 * @param string $key Key to name cache
		 * @param mixed $content Content to cache
		 * @param int $ttl time to live cache
		 * @return FcPhp\Cache\Interfaces\ICache
		 */
		public function set(string $key, $content, int $ttl) :ICache
		{
			$content = serialize($content);
			$this->write($key, $content, $ttl);
			return $this;
		}

		/**
		 * Method to verify if cache exists
		 *
		 * @param string $key Key to name cache
		 * @return bool
		 */
		public function has(string $key) :bool
		{
			if($this->isRedis()) {
				return $this->redis->get($key) ? true : false;
			}else{
				return file_exists($this->path . '/' . $key . '.cache');
			}
		}

		/**
		 * Method to delete cache
		 *
		 * @param string $key Key to name cache
		 * @return void
		 */
		public function delete(string $key) :void
		{
			if($this->isRedis()) {
				$this->redis->delete($key);
			}else{
				$this->fdelete($key);
			}
		}

		/**
		 * Method to verify/read cache
		 *
		 * @param string $key Key to name cache
		 * @return mixed
		 */
		public function get(string $key)
		{
			$content = $this->read($key);
			$content = explode('|', $content);
			$time = $content[0];
			$content = $content[1];
			if($time < time()) {
				$this->delete($key);
				return null;
			}
			return unserialize(base64_decode($content));
		}

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
				$this->redis->set($key, $content);
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
				return $this->redis->get($key);
			}else{
				return $this->fread($key);
			}
		}

		/**
		 * Method to write cache in file
		 *
		 * @param string $key Key to name cache
		 * @param string $content Content to cache
		 * @return void
		 */
		private function fmake(string $key, string $content) :void
		{
			$fopen = fopen($this->path . '/' . $key . '.cache', 'w');
			fwrite($fopen, $content);
			fclose($fopen);
		}

		/**
		 * Method to read cache in file
		 *
		 * @param string $key Key to name cache
		 * @return mixed
		 */
		private function fread(string $key)
		{
			$file = $this->path . '/' . $key . '.cache';
			return $this->has($key) ? file_get_contents($file) : null;
		}

		/**
		 * Method to delete cache in file
		 *
		 * @param string $key Key to name cache
		 * @return void
		 */
		private function fdelete(string $key) :void
		{
			$file = $this->path . '/' . $key . '.cache';
			if($this->has($key)) {
				unlink($file);
			}
		}

		/**
		 * Method to verify if Cache use Redis
		 *
		 * @return bool
		 */
		private function isRedis()
		{
			return $this->strategy == 'redis';
		}
	}
}