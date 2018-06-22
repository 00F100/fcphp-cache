<?php

namespace FcPhp\Cache\Traits
{
	use Exception;
	use FcPhp\Crypto\Interfaces\ICrypto;
	use FcPhp\Cache\Exceptions\PathNotPermissionFoundException;
	use FcPhp\Crypto\Crypto;

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
			$content = time() + $ttl . '|' . $this->processContent($key, $content);
			if($this->isRedis()) {
				$this->redis->set(self::CACHE_REDIS_ALIAS . $key, $content);
			}else{
				$this->fmake($key, $content);
			}
		}

		private function processContent(string $key, string $content)
		{
			if($this->crypto instanceof ICrypto) {
				$key = $this->getKey(md5($key));
				return $this->crypto->encode($key, $content);
			}
			return base64_encode($content);
		}

		private function unprocessContent(string $key, string $content)
		{
			if($this->crypto instanceof ICrypto) {
				$key = $this->getKey(md5($key));
				return $this->crypto->decode($key, $content);
			}
			return base64_decode($content);
		}

		private function getKey(string $hash)
		{
			if(!is_dir($this->pathKeys)) {
				try {
					mkdir($this->pathKeys, 0755, true);
				} catch (Exception $e) {
					throw new PathNotPermissionFoundException($this->pathKeys, 500, $e);
				}
			}
			$filePath = $this->pathKeys . '/' . $hash . '.key';
			if(file_exists($filePath)) {
				return file_get_contents($filePath);
			}
			$key = Crypto::getKey();
			$fopen = fopen($filePath, 'w');
			fwrite($fopen, $key);
			fclose($fopen);
			return $key;
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