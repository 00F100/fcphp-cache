# FcPhp Cache

Package to manage Cache Index and crypto content using [Sodium PHP](http://php.net/manual/en/book.sodium.php) _(optional)_

[![Build Status](https://travis-ci.org/00F100/fcphp-cache.svg?branch=master)](https://travis-ci.org/00F100/fcphp-cache) [![codecov](https://codecov.io/gh/00F100/fcphp-cache/branch/master/graph/badge.svg)](https://codecov.io/gh/00F100/fcphp-cache) [![Total Downloads](https://poser.pugx.org/00F100/fcphp-cache/downloads)](https://packagist.org/packages/00F100/fcphp-cache)

## How to install

Composer:
```sh
$ composer require 00f100/fcphp-cache
```

or add in composer.json
```json
{
	"require": {
		"00f100/fcphp-cache": "*"
	}
}
```

## How to use

```php
<?php

use FcPhp\Cache\Facades\CacheFacade;
use FcPhp\Crypto\Crypto;

/**
 * Method to create new instance of Cache
 *
 * @param string|array $cacheRepository Configuration of redis or path to save files cache
 * @param string $nonce Nonce to use crypto into content of cache. To generate: \FcPhp\Crypto\Crypto::getNonce()
 * @param string $pathKeys Path to save keys crypto
 * @return FcPhp\Cache\Interfaces\ICache
 */
$cache = CacheFacade::getInstance(string|array $cacheRepository, string $nonce = null, string $pathKeys = null);

/*
	To use with Redis
	=========================
*/
$redis = [
	'host' => '127.0.0.1',
	'port' => '6379',
	'password' => null,
	'timeout' => 100,
];
$cache = CacheFacade::getInstance($redis);
/*
	To use with Redis and crypto
	=========================
*/
$redis = [
	'host' => '127.0.0.1',
	'port' => '6379',
	'password' => null,
	'timeout' => 100,
];
$cache = CacheFacade::getInstance($redis, Crypto::getNonce(), 'path/to/keys');

/*
	To use with file
	=========================
*/
$cache = CacheFacade::getInstance('path/to/cache');

/*
	To use with file and crypto
	=========================
*/
$cache = CacheFacade::getInstance('path/to/cache', Crypto::getNonce(), 'path/to/keys');

/**
 * Method to create new cache
 *
 * @param string $key Key to name cache
 * @param mixed $content Content to cache
 * @param int $ttl time to live cache
 * @return FcPhp\Cache\Interfaces\ICache
 */
$cache->set(string $key, $content, int $ttl) :ICache

/**
 * Method to verify if cache exists
 *
 * @param string $key Key to name cache
 * @return bool
 */
$cache->has(string $key) :bool

/**
 * Method to verify/read cache
 *
 * @param string $key Key to name cache
 * @return mixed
 */
$cache->get(string $key)

/**
 * Method to delete cache
 *
 * @param string $key Key to name cache
 * @return void
 */
$cache->delete(string $key) :void

/**
 * Method to clean old caches
 *
 * @return FcPhp\Cache\Interfaces\ICache
 */
$cache->clean() :ICache

```