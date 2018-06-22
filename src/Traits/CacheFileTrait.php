<?php

namespace FcPhp\Cache\Traits
{
	use Exception;
	use FcPhp\Cache\Exceptions\PathNotPermissionFoundException;

	trait CacheFileTrait
	{
		/**
		 * Method to write cache in file
		 *
		 * @param string $key Key to name cache
		 * @param string $content Content to cache
		 * @return void
		 */
		private function fmake(string $key, string $content) :void
		{
			if(!is_dir($this->path)) {
				try {
					mkdir($this->path, 0755, true);
				} catch (Exception $e) {
					throw new PathNotPermissionFoundException($this->path, 500, $e);
				}
			}
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
			if($this->has($key)) {
				$file = $this->path . '/' . $key . '.cache';
				unlink($file);
			}
		}

		/**
		 * Method to clean old caches
		 *
		 * @return void
		 */
		private function fclean() :void
		{
			if(is_dir($this->path)) {
				$list = array_diff(scandir($this->path), ['.','..']);
				if(count($list) > 0) {
					foreach($list as $file) {
						$fileData = explode('.', $file);
						$key = current($fileData);
						if(end($fileData) == 'cache') {
							$content = file_get_contents($this->path . '/' . $file);
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
}