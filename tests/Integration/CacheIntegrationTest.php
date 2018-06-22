<?php

use FcPhp\Cache\Cache;
use FcPhp\Cache\Interfaces\ICache;
use PHPUnit\Framework\TestCase;
use FcPhp\Cache\Facades\CacheFacade;

class CacheIntegrationTest extends TestCase
{
	private $instance;

	public function setUp()
	{
		$redis = [
			'host' => 'redis.docker',
			'port' => '6379',
			'password' => null,
			'timeout' => 100
		];
		$this->instance = CacheFacade::getInstance($redis, null);
	}

	public function testInstance()
	{
		$this->assertTrue($this->instance instanceof ICache);
	}

	public function testSetGet()
	{
		$key = md5(microtime() . rand());
		$content = ['data' => 'value'];
		$ttl = 10;
		$this->instance->set($key, $content, $ttl);
		$this->assertEquals($this->instance->get($key), $content);
	}

	public function testHas()
	{
		$key = md5(microtime() . rand());
		$content = ['data' => 'value'];
		$ttl = 10;
		$this->instance->set($key, $content, $ttl);
		$this->assertTrue($this->instance->has($key));
	}

	public function testCleanRedis()
	{
		$this->assertTrue($this->instance->clean() instanceof ICache);
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
		$ttl = 10;
		$this->assertTrue($instance->get($key) == null);
		$instance->delete($key);
	}

	public function testSetGetFile()
	{
		CacheFacade::reset();
		$instance = CacheFacade::getInstance(null, 'tests/var/cache');
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
		$this->assertTrue($instance->has($key));
	}

	public function testClean()
	{
		$this->assertTrue($this->instance->clean() instanceof ICache);
	}
}