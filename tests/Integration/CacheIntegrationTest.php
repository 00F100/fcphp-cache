<?php

use FcPhp\Cache\Cache;
use FcPhp\Cache\Interfaces\ICache;
use FcPhp\Cache\Facades\CacheFacade;
use FcPhp\Crypto\Crypto;
use PHPUnit\Framework\TestCase;

class CacheIntegrationTest extends TestCase
{
	private $instance;

	public function setUp()
	{
		$redis = [
			'host' => '127.0.0.1',
			'port' => '6379',
			'password' => null,
			'timeout' => 100
		];
		$this->instance = CacheFacade::getInstance($redis, Crypto::getNonce(), 'tests/var/keys');
	}

	public function testInstance()
	{
		$this->assertTrue($this->instance instanceof ICache);
	}

	public function testSetGet()
	{
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 10;
		$this->instance->set($key, $content, $ttl);
		$this->assertEquals($this->instance->get($key), $content);
	}

	public function testHas()
	{
		$key = 'cb6e0e439eff1d257641502d8fa65698';
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
		$instance = CacheFacade::getInstance('tests/var/cache');
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
		$this->assertTrue($instance->has($key));
		$instance->clean();
	}

	public function testSetGetFileDelete()
	{
		CacheFacade::reset();
		$instance = CacheFacade::getInstance('tests/var/cache');
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
		$this->assertTrue(is_null($this->instance->get('abc')));
	}

	public function testClean()
	{
		$this->assertTrue($this->instance->clean() instanceof ICache);
	}

	/**
     * @expectedException FcPhp\Cache\Exceptions\PathKeyNotFoundException
     */
	public function testWithCryptoNonPath()
	{
		$instance = new Cache(null, 'tests/var/cache', new Crypto(Crypto::getNonce()));
	}

	/**
	 * @expectedException FcPhp\Cache\Exceptions\PathNotPermissionFoundException
	 */
	public function testWithCryptoNonPermissionPath()
	{
		$instance = new Cache(null, 'tests/var/cache', new Crypto(Crypto::getNonce()), '/root/dir');
		$key = 'cb6e0e439eff1d257641502d8fa65698';
		$content = ['data' => 'value'];
		$ttl = 0;
		$instance->set($key, $content, $ttl);
		$this->assertEquals($instance->get($key), $content);
	}

	public function testWithCryptoNonNewDir()
	{
		$instance = new Cache(null, 'tests/var/cache', new Crypto(Crypto::getNonce()), 'tsts/var/newdir');
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