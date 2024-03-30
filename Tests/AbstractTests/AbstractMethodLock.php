<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodLock extends WebTestCase
{
	protected string $method = 'LOCK';

	public function testIfNoPath()
	{
		$crawler = $this->client->request($this->method, '/webdavtest', server: ['HTTP_timeout' => 'Second-100']);

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt', server: ['HTTP_timeout' => 'Second-100']);

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExistsWithoutTimeout()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExistsInfiniteWithoutTimeout()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_timeout' => 'Second-100']);

		$this->assertResponseStatusCodeSame(200);
		// $this->assertEquals('', $this->client->getResponse()->getContent()); //--TODO:check response
	}

	public function testIfFileExistsInfinite()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_timeout' => 'Second-100']);

		$this->assertResponseStatusCodeSame(200);
		// $this->assertEquals('', $this->client->getResponse()->getContent()); //--TODO:check response
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_timeout' => 'Infinite']);

		$this->assertResponseStatusCodeSame(200);
		// $this->assertEquals('', $this->client->getResponse()->getContent()); //--TODO:check response
	}

	public function testIfFOlderExistsInfinite()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_timeout' => 'Infinite']);

		$this->assertResponseStatusCodeSame(200);
		// $this->assertEquals('', $this->client->getResponse()->getContent()); //--TODO:check response
	}
}
