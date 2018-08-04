<?php

namespace FcPhp\Cache\Interfaces
{
    use FcPhp\Cache\Interfaces\ICache;
    use FcPhp\Redis\Interfaces\IRedis;
    use FcPhp\Crypto\Interfaces\ICrypto;
    
    interface ICache
    {
        /**
         * Method to construct new instance of Cache
         *
         * @param FcPhp\Redis\Interfaces\IRedis $redis Redis instance
         * @param string $path Path to cache in file
         * @return void
         */
        public function __construct(?IRedis $redis = null, string $path = null, ?ICrypto $crypto = null, string $pathKeys = null);

        /**
         * Method to create new cache
         *
         * @param string $key Key to name cache
         * @param mixed $content Content to cache
         * @param int $ttl time to live cache
         * @return FcPhp\Cache\Interfaces\ICache
         */
        public function set(string $key, $content, int $ttl) :ICache;

        /**
         * Method to verify if cache exists
         *
         * @param string $key Key to name cache
         * @return bool
         */
        public function has(string $key) :bool;

        /**
         * Method to delete cache
         *
         * @param string $key Key to name cache
         * @return void
         */
        public function delete(string $key) :void;

        /**
         * Method to verify/read cache
         *
         * @param string $key Key to name cache
         * @return mixed
         */
        public function get(string $key);

        /**
         * Method to clean old caches
         *
         * @return FcPhp\Cache\Interfaces\ICache
         */
        public function clean() :ICache;
    }
}
