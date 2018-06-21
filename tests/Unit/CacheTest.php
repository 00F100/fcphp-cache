<?php

use FcPhp\Cache\Cache;
use FcPhp\Cache\Interfaces\ICache;
use PHPUnit\Framework\TestCase;

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
		$ttl = 84000;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
		$this->assertTrue($instance->has($key));
		$instance->delete($key);
	}
}