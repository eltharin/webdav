<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodUnlock extends WebTestCase
{
	protected string $method = 'UNLOCK';

	public function testIfNoPath()
	{
		$crawler = $this->client->request($this->method, '/webdavtest');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt', server: ['HTTP_lock-Token' => '12345678-1234-5678-abcd-abcdef012345']);

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExistsNoLock()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_lock-Token' => '12345678-1234-5678-abcd-abcdef012345']);

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFOlderExistsNoLock()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_lock-Token' => '12345678-1234-5678-abcd-abcdef012345']);

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExistsNoToken()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFOlderExistsNoToken()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExistsBadToken()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_lock-Token' => 'badtoken']);

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFOlderExistsBadToken()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_lock-Token' => 'badtoken']);

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_lock-Token' => '<opaquelocktoken:12345678-1234-5678-abcd-abcdef012345>']);

		$this->assertResponseStatusCodeSame(204);
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_lock-Token' => '<opaquelocktoken:12345678-1234-5678-abcd-abcdef012345>']);

		$this->assertResponseStatusCodeSame(204);
	}
}
