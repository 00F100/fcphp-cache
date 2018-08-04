<?php

namespace FcPhp\Cache
{
    use FcPhp\Cache\Interfaces\ICache;
    use FcPhp\Redis\Interfaces\IRedis;
    use FcPhp\Cache\Traits\CacheTrait;
    use FcPhp\Cache\Traits\CacheFileTrait;
    use FcPhp\Cache\Traits\CacheRedisTrait;
    use FcPhp\Crypto\Interfaces\ICrypto;
    use FcPhp\Cache\Exceptions\PathKeyNotFoundException;

    class Cache implements ICache
    {
        /**
         * Const to alias cache in Redis
         */
        const CACHE_REDIS_ALIAS = 'cache::';

        const CACHE_KEY_ALIAS = 'key::';

        use CacheTrait;
        use CacheFileTrait;
        use CacheRedisTrait;

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
         * @var FcPhp\Crypto\Interfaces\ICrypto
         */
        private $crypto = null;

        /**
         * @var string
         */
        private $pathKeys = null;

        /**
         * Method to construct new instance of Cache
         *
         * @param FcPhp\Redis\Interfaces\IRedis $redis Redis instance
         * @param string $path Path to cache in file
         * @return void
         */
        public function __construct(?IRedis $redis = null, string $path = null, ?ICrypto $crypto = null, string $pathKeys = null)
        {
            if($redis instanceof IRedis) {
                $this->redis = $redis;
            }else{
                $this->path = $path;
                $this->strategy = 'path';
            }
            if($crypto instanceof ICrypto) {
                $this->crypto = $crypto;
                if(empty($pathKeys)) {
                    throw new PathKeyNotFoundException();
                }
                $this->pathKeys = $pathKeys;
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
                return $this->redis->get(self::CACHE_REDIS_ALIAS . $key) ? true : false;
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
                $this->redis->delete(self::CACHE_REDIS_ALIAS . $key);
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
            if(!empty($content)) {
                $content = explode('|', $content);
                $time = $content[0];
                $content = $content[1];
                $content = $this->unprocessContent($key, $content);
                if($time < time()) {
                    $this->delete($key);
                    return null;
                }
                return unserialize($content);
            }
            return null;
        }

        /**
         * Method to clean old caches
         *
         * @return FcPhp\Cache\Interfaces\ICache
         */
        public function clean() :ICache
        {
            if($this->isRedis()) {
                $this->redisClean();
            }else{
                $this->fclean();
            }
            return $this;
        }
    }
}
