<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll;

use DateTimeImmutable;
use Eltharin\WebdavBundle\Tests\AbstractTests\AbstractMethodLock;

class MethodLockTest extends AbstractMethodLock
{
	use TraitFilesForAllTests;

	public function testIfFileExists2()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_timeout' => 'Second-100']);

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock');

		$content = json_decode(file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock'));
		$expire = ((new DateTimeImmutable($content->timeout))->format('U') - (new DateTimeImmutable())->format('U'));

		$this->assertEquals('XXX', $content->owner);
		$this->assertTrue($expire <= 100 && $expire > 95);
	}

	public function testIfFileExists3()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_timeout' => 'Second-100'], content: '<?xml version="1.0" encoding="utf-8" ?><D:lockinfo xmlns:D="DAV:"><D:owner><D:href>Dudule</D:href></D:owner></D:lockinfo>');

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock');

		$content = json_decode(file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock'));
		$expire = ((new DateTimeImmutable($content->timeout))->format('U') - (new DateTimeImmutable())->format('U'));

		$this->assertEquals('Dudule', $content->owner);
		$this->assertTrue($expire <= 100 && $expire > 95);
	}

	public function testIfFileExists2Infinite()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_timeout' => 'Infinite']);

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock');

		$content = json_decode(file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock'));

		$this->assertEquals('XXX', $content->owner);
		$this->assertEquals('2099-12-31', (new DateTimeImmutable($content->timeout))->format('Y-m-d'));
	}

	public function testIfFOlderExists2()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_timeout' => 'Second-100']);

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'folder1~.lock');

		$content = json_decode(file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'folder1~.lock'));
		$expire = ((new DateTimeImmutable($content->timeout))->format('U') - (new DateTimeImmutable())->format('U'));

		$this->assertEquals('XXX', $content->owner);
		$this->assertTrue($expire <= 100 && $expire > 95);
	}

	public function testIfFOlderExists2Infinite()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_timeout' => 'Infinite']);

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'folder1~.lock');

		$content = json_decode(file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'folder1~.lock'));
		$expire = (-(new DateTimeImmutable())->format('U'));

		$this->assertEquals('XXX', $content->owner);
		$this->assertEquals('2099-12-31', (new DateTimeImmutable($content->timeout))->format('Y-m-d'));
	}
}
