<?php

use FcPhp\Cache\Cache;
use FcPhp\Cache\Interfaces\ICache;
use PHPUnit\Framework\TestCase;
use FcPhp\Crypto\Interfaces\ICrypto;

class CacheTest extends TestCase
{
	private $instance;

	public function setUp()
	{
		$redisInstance = $this->createMock('FcPhp\Redis\Interfaces\IRedis');
		$redisInstance
			->expects($this->any())
			->method('get')
			->will($this->returnValue(time() . '|' . base64_encode(serialize(['data' => 'value']))));
		$redisInstance
			->expects($this->any())
			->method('keys')
			->will($this->returnValue([Cache::CACHE_REDIS_ALIAS . '1', Cache::CACHE_REDIS_ALIAS . '2', Cache::CACHE_REDIS_ALIAS . '3']));

		$this->instance = new Cache($redisInstance);
	}

	public function testInstance()
	{
		$this->assertTrue($this->instance instanceof ICache);
	}

	public function testSetGet()
	{
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 84000;
		$this->instance->set($key, $content, $ttl);
		$this->assertEquals($this->instance->get($key), $content);
	}

	public function testHas()
	{
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 84000;
		$this->instance->set($key, $content, $ttl);
		$this->assertTrue($this->instance->has($key));
	}

	public function testSetGetOld()
	{
		$redisInstance = $this->createMock('FcPhp\Redis\Interfaces\IRedis');
		$redisInstance
			->expects($this->any())
			->method('get')
			->will($this->returnValue(123 . '|' . base64_encode(serialize(['data' => 'value']))));

		$instance = new Cache($redisInstance);


		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 84000;
		$instance->set($key, $content, $ttl);
		$this->assertTrue($instance->get($key) == null);
	}

	public function testSetGetFile()
	{
		$instance = new Cache(null, 'tests/var/cache');
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
		$this->assertTrue($instance->has($key));
		$instance->delete($key);
	}

	public function testGetKeyNonExists()
	{
		$instance = new Cache(null, 'tests/var/cache');
		$this->assertTrue(is_null($instance->get('abc')));
	}

	public function testClean()
	{
		$this->assertTrue($this->instance->clean() instanceof ICache);
	}

	public function testCleanFile()
	{
		$instance = new Cache(null, 'tests/var/cache');
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertTrue($instance->clean() instanceof ICache);
	}

	public function testWithCrypto()
	{
		$cryptoInstance = $this->createMock('FcPhp\Crypto\Interfaces\ICrypto');
		$cryptoInstance
			->expects($this->any())
			->method('decode')
			->will($this->returnValue(serialize(['data' => 'value'])));

		$instance = new Cache(null, 'tests/var/cache', $cryptoInstance, 'tests/var/keys');
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
	}

	/**
     * @expectedException FcPhp\Cache\Exceptions\PathKeyNotFoundException
     */
	public function testWithCryptoNonPath()
	{
		$cryptoInstance = $this->createMock('FcPhp\Crypto\Interfaces\ICrypto');
		$cryptoInstance
			->expects($this->any())
			->method('decode')
			->will($this->returnValue(serialize(['data' => 'value'])));

		$instance = new Cache(null, 'tests/var/cache', $cryptoInstance);
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
	}

	/**
	 * @expectedException FcPhp\Cache\Exceptions\PathNotPermissionFoundException
	 */
	public function testWithCryptoNonPermissionPath()
	{
		$cryptoInstance = $this->createMock('FcPhp\Crypto\Interfaces\ICrypto');
		$cryptoInstance
			->expects($this->any())
			->method('decode')
			->will($this->returnValue(serialize(['data' => 'value'])));

		$instance = new Cache(null, 'tests/var/cache', $cryptoInstance, '/root/dir');
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
	}

	public function testWithCryptoNonNewDir()
	{
		$cryptoInstance = $this->createMock('FcPhp\Crypto\Interfaces\ICrypto');
		$cryptoInstance
			->expects($this->any())
			->method('decode')
			->will($this->returnValue(serialize(['data' => 'value'])));

		$instance = new Cache(null, 'tests/var/cache', $cryptoInstance, 'tests/var/newdir');
		$key = md5(time() . rand());
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
		$instance->delete($key);
	}

	/**
	 * @expectedException FcPhp\Cache\Exceptions\PathNotPermissionFoundException
	 */
	public function testDirCacheNonPermission()
	{
		$instance = new Cache(null, '/root/dir');
		$key = md5(time() . rand());
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
	}
}