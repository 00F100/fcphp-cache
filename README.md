# FcPhp Cache

Package to manage Cache Index

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

/**
 * Method to create new instance of Cache
 *
 * @param array $redis Configuration of redis
 * @param string $path Path to cache in file
 * @return FcPhp\Cache\Interfaces\ICache
 */
$cache = CacheFacade::getInstance(?array $redis, string $path = null);

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
$cache = CacheFacade::getInstance($redis, null);

/*
	To use with file
	=========================
*/
$cache = CacheFacade::getInstance(null, 'path/to/dir');

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